<!-- Estilos -->
<style>
    #drop-area {
        border: 8px dashed #007bff;
        padding: 20px;
        cursor: pointer;
    }
    
    /* Estilos para o carrossel de entidades */
    .entity-carousel-control {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.95);
        border: 1px solid #e1e3ea;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .entity-carousel-control:hover {
        background-color: #fff;
        border-color: #b5b5c3;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transform: translateY(-50%) scale(1.05);
    }
    
    .entity-carousel-control:active {
        transform: translateY(-50%) scale(0.95);
    }
    
    .carousel-control-prev.entity-carousel-control {
        left: -20px;
    }
    
    .carousel-control-next.entity-carousel-control {
        right: -20px;
    }
    
    /* Melhorar área clicável do link do carrossel */
    #kt_sliders_widget_2_slider .carousel-item > a {
        padding: 1rem;
        transition: background-color 0.2s ease;
        border-radius: 0.5rem;
        display: block;
    }
    
    #kt_sliders_widget_2_slider .carousel-item > a:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
    
    /* Responsividade do carrossel */
    @media (max-width: 768px) {
        .entity-carousel-control {
            width: 32px;
            height: 32px;
        }
        
        .carousel-control-prev.entity-carousel-control {
            left: -10px;
        }
        
        .carousel-control-next.entity-carousel-control {
            right: -10px;
        }
        
        #kt_sliders_widget_2_slider .carousel-item > a {
            padding: 0.75rem;
        }
    }
</style>
