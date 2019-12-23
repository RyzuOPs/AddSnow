<div id="snow"></div>
<script>
document.addEventListener('DOMContentLoaded', function(){
    var script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js';
    script.onload = function(){
        particlesJS("snow", {
            "particles": {
                "number": {
                    "value": 200,
                    "density": {
                        "enable": true,
                        "value_area": {$addsnow_particles_number_density_value_area}
                    }
                },
                "color": {
                    "value": "{$addsnow_particles_color_value}"
                },
                "opacity": {
                    "value": {$addsnow_particles_opacity_value},
                    "random": false,
                    "anim": {
                        "enable": false
                    }
                },
                "size": {
                    "value": {$addsnow_particles_size_value},
                    "random": true,
                    "anim": {
                        "enable": false
                    }
                },
                "line_linked": {
                    "enable": false
                },
                "move": {
                    "enable": true,
                    "speed": {$addsnow_particles_move_speed},
                    "direction": "bottom",
                    "random": true,
                    "straight": false,
                    "out_mode": "out",
                    "bounce": false,
                    "attract": {
                        "enable": true,
                        "rotateX": 300,
                        "rotateY": 1200
                    }
                }
            },
            "interactivity": {
                "events": {
                    "onhover": {
                        "enable": {$addsnow_interactivity_events_onhoover}
                    },
                    "onclick": {
                        "enable": false
                    },
                    "resize": false
                }
            },
            "retina_detect": true
        });
    }
    document.head.append(script);
});
</script>