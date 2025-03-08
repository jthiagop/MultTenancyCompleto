<!--begin::Modal - Selecionar Localização-->
<div class="modal fade" id="kt_modal_select_location" tabindex="-1" aria-labelledby="modalSelectLocationLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <!--begin::Modal header-->
        <div class="modal-header border-bottom-0">
          <h5 class="modal-title" id="modalSelectLocationLabel">Selecionar Localização</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <!--end::Modal header-->

        <!--begin::Modal body-->
        <div class="modal-body position-relative p-0">
          <!-- Mapa no Modal -->
          <div id="kt_modal_select_location_map" class="w-100" style="height:450px"></div>

          <!-- Formulário em overlay, posicionado sobre o mapa -->
          <form id="locationForm" method="POST" action="/save-location" class="position-absolute top-0 end-0 m-3 p-3 bg-white rounded shadow" style="z-index: 10; width: 260px;">
            <!-- Token CSRF e ID do patrimônio -->
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="id" id="patrimonio_id" value="{{ $patrimonio->id }}">

            <div class="form-floating mb-2">
              <input type="text" class="form-control" id="latitude" name="latitude" placeholder="Latitude" value="{{ $patrimonio->latitude }}" readonly>
              <label for="latitude">Latitude</label>
            </div>
            <div class="form-floating mb-2">
              <input type="text" class="form-control" id="longitude" name="longitude" placeholder="Longitude" value="{{ $patrimonio->longitude }}" readonly>
              <label for="longitude">Longitude</label>
            </div>
            <button type="submit" class="btn btn-primary w-100">Aplicar</button>
          </form>
        </div>
        <!--end::Modal body-->

        <!--begin::Modal footer-->
        <div class="modal-footer border-top-0 justify-content-end">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
        <!--end::Modal footer-->
      </div>
    </div>
  </div>
  <!--end::Modal - Selecionar Localização-->
  <script>
    let modalMap, modalMarker, mainMap, mainMarker;

    // Função que utiliza o CEP para obter as coordenadas via Google Geocoding API
    function geocodeCEP(cep, callback) {
      const url = `https://maps.googleapis.com/maps/api/geocode/json?address=${cep}&key={{ env('GOOGLE_MAPS_API_KEY') }}`;
      fetch(url)
        .then(response => response.json())
        .then(data => {
          if (data.status === "OK") {
            const location = data.results[0].geometry.location;
            callback(location);
          } else {
            console.error("Geocodificação falhou: " + data.status);
            // Localização padrão (São Paulo) se a geocodificação falhar
            callback({ lat: -23.550520, lng: -46.633308 });
          }
        })
        .catch(error => {
          console.error("Erro na requisição de geocodificação:", error);
          callback({ lat: -23.550520, lng: -46.633308 });
        });
    }

    // Inicializa ambos os mapas utilizando a localização dos campos ou, se não disponíveis, via CEP
    function initAllMaps() {
      const cep = "{{ $patrimonio->cep }}";
      const latFromDB = "{{ $patrimonio->latitude }}";
      const lngFromDB = "{{ $patrimonio->longitude }}"; // Note: 'logintude' pode ser um typo de 'longitude'

      if (latFromDB && lngFromDB) {
        // Se os valores de latitude e longitude estiverem disponíveis, utiliza-os
        const location = { lat: parseFloat(latFromDB), lng: parseFloat(lngFromDB) };
        initMap(location);
        initModalMap(location);
      } else {
        // Caso contrário, utiliza o CEP para geocodificação
        geocodeCEP(cep, function(location) {
          initMap(location);
          initModalMap(location);
        });
      }
    }

    // Inicializa o mapa principal
    function initMap(location) {
      mainMap = new google.maps.Map(document.getElementById('map'), {
        zoom: 15,
        center: location
      });
      mainMarker = new google.maps.Marker({
        position: location,
        map: mainMap
      });
    }

    // Inicializa o mapa do modal com interatividade
    function initModalMap(initialLocation) {
      // Usa os valores dos inputs, se disponíveis, ou a localização obtida
      let currentLat = parseFloat(document.getElementById('latitude').value) || initialLocation.lat;
      let currentLng = parseFloat(document.getElementById('longitude').value) || initialLocation.lng;
      let defaultLocation = { lat: currentLat, lng: currentLng };

      modalMap = new google.maps.Map(document.getElementById('kt_modal_select_location_map'), {
        zoom: 15,
        center: defaultLocation
      });

      modalMarker = new google.maps.Marker({
        position: defaultLocation,
        map: modalMap,
        draggable: true
      });

      // Atualiza os inputs quando o marcador é arrastado
      modalMarker.addListener('dragend', function(event) {
        document.getElementById('latitude').value = event.latLng.lat().toFixed(6);
        document.getElementById('longitude').value = event.latLng.lng().toFixed(6);
      });

      // Atualiza os inputs quando o usuário clica no mapa
      modalMap.addListener('click', function(event) {
        modalMarker.setPosition(event.latLng);
        document.getElementById('latitude').value = event.latLng.lat().toFixed(6);
        document.getElementById('longitude').value = event.latLng.lng().toFixed(6);
      });
    }

    // Função para salvar a nova localização via AJAX e atualizar o registro na tabela "patrimonios"
    function saveLocation() {
      const id = document.getElementById('patrimonio_id').value;
      const latitude = document.getElementById('latitude').value;
      const longitude = document.getElementById('longitude').value;

      console.log("Localização selecionada: " + latitude + ", " + longitude);

      fetch('/save-location', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ id: id, latitude: latitude, longitude: longitude })
      })
      .then(response => response.json())
      .then(data => {
        console.log('Localização salva com sucesso:', data);
        // Atualiza o mapa principal com a nova localização
        if (mainMarker && mainMap) {
          const newLocation = { lat: parseFloat(latitude), lng: parseFloat(longitude) };
          mainMarker.setPosition(newLocation);
          mainMap.setCenter(newLocation);
        }
      })
      .catch(error => console.error('Erro ao salvar localização:', error));
    }
  </script>

  <!-- Carrega a API do Google Maps e chama initAllMaps ao finalizar o carregamento -->
  <script async defer
    src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initAllMaps"></script>

  <!-- Inclua aqui os scripts do Bootstrap se necessário -->
