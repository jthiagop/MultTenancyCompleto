<!-- Campo oculto para identificar o tipo (receita ou despesa) -->
<input type="hidden" name="tipo_financeiro" id="tipo_financeiro" value="">
<input type="hidden" name="status_pagamento" id="status_pagamento" value="em aberto">
<input type="hidden" name="origem" id="origem" value="Banco">
<!-- Campo hidden para garantir que o tipo seja sempre enviado -->
<input type="hidden" name="tipo" id="tipo" value="">
<!-- Campo oculto para modo edição -->
<input type="hidden" name="transacao_id" id="transacao_id" value="">
<!-- Campo para method spoofing (PUT em modo edição) -->
<input type="hidden" name="_method" id="_method" value="POST">

