<!DOCTYPE html>
<html lang="es">

<head>
    <?php
    // ... (PHP head) ...
    include("../includes/head-tag-contents.php");
    include('connectdb.php');
    require 'functions.php';

    $llaveejemplar = isset($_GET['id']) ? $_GET['id'] : '';
    $enciclovida = new enciclovida();
    $urlComments = $enciclovida->ligaComentarios($mysqli, $llaveejemplar);
    list(
        $scientificName,
        $autor,
        $commonName,
        $region,
        $localidad,
        $procedenciaejemplar,
        $col,
        $ins,
        $lat,
        $lon,
        $fechacolecta,
        $colector,
        $datum,
        $ultimafechaactualizacion,
        $urlejemplar,
        $licenciauso,
        $formadecitar,
        $reino,
        $phylumdivision,
        $clase,
        $orden,
        $familia,
        $genero,
        $categoriainfraespecie,
        $fechadeterminacion,
        $numcatalogo,
        $numcolecta,
        $determinador,
        $obsusoinfo,
        $tipoPreparacion,
        $numeroindividuos,
        $persona,
        $reinocatvalido,
        $divisionphylumcatvalido,
        $clasecatvalido,
        $ordencatvalido,
        $familiacatvalido,
        $generocatvalido,
        $epitetoespecificocatvalido,
        $categoriainfraespeciecatvalido,
        $epitetoinfraespecificocatvalido,
        $reinooriginal,
        $divisionphylumoriginal,
        $claseoriginal,
        $ordenoriginal,
        $familiaoriginal,
        $generooriginal,
        $epitetoespecificooriginal,
        $epitetoinfraespecificooriginal,
        $categoriainfraespecieoriginal,
        $nombrevalidocatscat,
        $nombreoriginallimpioscat,
        $categoriacatscat,
        $categoriaoriginalscat,
        $autoranioespeciecat,
        $autoranioinfraespeciecat,
        $autoranioespecieoriginal,
        $autoranioinfraespecieoriginal,
        $estatusespeciecat,
        $estatusinfraespeciecat,
        $estatusespecieoriginal,
        $estatusinfraespecieoriginal,
        $catdiccespeciecat,
        $catdiccinfraespeciecat,
        $catdiccespecieoriginal,
        $catdiccinfraespecieoriginal,
        $endemismo,
        $iucn,
        $cites,
        $nom059,
        $prioritaria,
        $exoticainvasora,
        $paisoriginal,
        $estadooriginal,
        $municipiooriginal,
        $geoposmapagacetlitetiq,
        $usvserieVII,
        $altitudmapa,
        $altitudinicialejemplar,
        $paismapa,
        $estadomapa,
        $municipiomapa,
        $datumoriginal,
        $tipovegetacion,
        $fuentegeorreferenciacion,
        $coordenadaDescripcion,
        $tipovegetacionmapa,
        $incertidumbreXY,
        $observacionescoordenadasconabio

    ) = $enciclovida->obtenResumen($mysqli, $llaveejemplar);
    $titulo = $enciclovida->obtenProyecto($mysqli, $llaveejemplar);
    $mysqli->close();

    $coordenadas_validas_para_mapa = (isset($lat) && is_numeric($lat) && isset($lon) && is_numeric($lon));

    function tieneDatoSignificativo($value)
    {
        if ($value === null) {
            return false;
        }
        $trimmedValue = trim((string)$value);
        if ($trimmedValue === '') {
            return false;
        }
        $noDataStrings = ['n/a', 'no disponible', 'sin dato', 'null'];
        if (in_array(strtolower($trimmedValue), $noDataStrings, true)) {
            return false;
        }
        return true;
    }
    ?>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Leaflet.awesome-markers/2.0.2/leaflet.awesome-markers.css" integrity="sha512-cUoWMYmv4H9TGPZnझ्f9AFj7NnvDu3VVJctcw+5+246oDf0CLRh+jVIsiQbdxfjGkYPdIYzjBJpdDCDBePWAQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* ... (tus estilos CSS se mantienen igual) ... */
        #map {
            height: 400px;
            width: 100%;
            margin-bottom: 20px;
        }

        h2 {
            font-family: sans-serif;
            font-weight: bold;
            margin-top: 40px;
            margin-bottom: 20px;
        }

        #tabla-geografica thead th {
            background-color: #9B2247;
            color: rgb(255, 255, 255);
            vertical-align: middle;
        }

        #tabla-recurso tbody td:first-child {
            font-weight: bold;
            width: 35%;
        }

        .table {
            margin-bottom: 30px;
        }

        .col-md-6 p {
            margin-bottom: 0.2rem;
        }

        .leaflet-popup-content div {
            margin-bottom: 2px;
        }

        .leaflet-popup-content div:last-child {
            margin-bottom: 0;
        }

        .popup-warning {
            color: #FFA500; 
            margin-bottom: 10px !important;
            display: flex;
            align-items: center;
        }

        .popup-warning strong {
            margin-left: 5px;
        }

        .leaflet-popup-content-wrapper {
            max-width: 300px;
        }

        .warning-icon { /* Usado en popups normales */
            font-size: 1.9em;
            margin-right: 5px;
            line-height: 1;
        }

        .custom-info-divicon {
            background: white;
            padding: 10px 15px;
            padding-right: 30px; 
            border-radius: 6px;
            box-shadow: 0 2px 7px rgba(0,0,0,0.45);
            font-size: 14px;
            font-family: Arial, Helvetica, sans-serif;
            color: #333;
            border: 1px solid #adadad;
            white-space: nowrap;
            text-align: center;
            position: relative; 
        }

        .custom-info-divicon-close {
            position: absolute;
            top: 0px;
            right: 3px;
            font-size: 20px; 
            font-weight: normal; 
            color: #757575; 
            text-decoration: none;
            cursor: pointer;
            padding: 5px; 
            line-height: 1;
            z-index: 10; 
        }
        .custom-info-divicon-close:hover {
            color: #000000;
        }
    </style>
</head>

<body>
    <?php 
    // ... (PHP body) ...
    include("../includes/navigation.php"); 
    ?>
    <div class="container" style="padding-top:15px;">
        <div class="card">
            <div style="background-color: #9B2247; color:rgb(255, 255, 255); " class="card-header">
                <h2 style="text-align:center; margin-top: 0; margin-bottom: 0;"><i><?php echo $nombrevalidocatscat; ?></i> <?php echo $autor; ?></h2>
            </div>
            <div class="card-body">
                <!-- ... (HTML Body content) ... -->
                <div class="row">
                    <div class="col-md-6">

                        <?php if (tieneDatoSignificativo($llaveejemplar)) : ?>
                            <p><b>Llave del ejemplar:</b> <?php echo htmlspecialchars($llaveejemplar); ?></p>
                        <?php endif; ?>

                        <?php if (tieneDatoSignificativo($procedenciaejemplar)) : ?>
                            <p><b>Procedencia del ejemplar:</b> <?php echo htmlspecialchars($procedenciaejemplar); ?></p>
                        <?php endif; ?>

                        <?php if (tieneDatoSignificativo($commonName)) : ?>
                            <p><b>Nombre común:</b> <?php echo htmlspecialchars($commonName); ?></p>
                        <?php endif; ?>

                        <?php if (tieneDatoSignificativo($region)) : ?>
                            <?php
                            $ubicacionCompleta = $region;
                            if (isset($localidad) && tieneDatoSignificativo($localidad)) {
                                $ubicacionCompleta .= " / " . $localidad;
                            }
                            ?>
                            <p><b>Ubicación:</b> <?php echo htmlspecialchars($ubicacionCompleta); ?></p>
                        <?php endif; ?>

                        <?php if (tieneDatoSignificativo($region)) : // Se usa $region como proxy para mostrar coordenadas ?>
                            <p><b>Coordenadas geográficas:</b> <?php
                                                                if ($coordenadas_validas_para_mapa) {
                                                                    echo "Latitud " . htmlspecialchars($lat) . ", longitud " . htmlspecialchars($lon);
                                                                } else {
                                                                    echo "Sin coordenadas";
                                                                }
                                                                ?></p>
                        <?php endif; ?>

                        <?php if (tieneDatoSignificativo($fechacolecta)) : ?>
                            <p><b>Fecha de colecta u observación:</b> <?php echo htmlspecialchars($fechacolecta); ?></p>
                        <?php endif; ?>

                        <?php
                        $valorColectorFinal = null;
                        if (isset($persona) && tieneDatoSignificativo($persona)) {
                            $valorColectorFinal = $persona;
                        } elseif (isset($colector) && tieneDatoSignificativo($colector)) {
                            $valorColectorFinal = $colector;
                        }
                        if ($valorColectorFinal !== null) :
                        ?>
                            <p><b>Colector u observador:</b> <?php echo htmlspecialchars($valorColectorFinal); ?></p>
                        <?php endif; ?>
                        <br>
                    </div>
                    <div class="col-md-6">


                        <?php if (tieneDatoSignificativo($col)) : ?>
                            <p><b>Colección:</b> <?php echo htmlspecialchars($col); ?></p>
                        <?php endif; ?>

                        <?php if (tieneDatoSignificativo($ins)) : ?>
                            <p><b>Institución:</b> <?php echo htmlspecialchars($ins); ?></p>
                        <?php endif; ?>

                        <?php if (tieneDatoSignificativo($titulo)) : ?>
                            <p><b>Proyecto: </b> <?php echo $titulo; ?></p>
                        <?php endif; ?>

                        <?php if (tieneDatoSignificativo($obsusoinfo)) : ?>
                            <p><b>Cuestiones en el ejemplar:</b> <?php echo htmlspecialchars($obsusoinfo); ?></p>
                        <?php endif; ?>
                        <br>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12" id="map">
                        <?php
                        if (!$coordenadas_validas_para_mapa) {
                            echo '<p style="text-align: center; padding-top: 20px; color: red;">No se puede mostrar en el mapa.</p>';
                        }
                        ?>
                    </div>
                </div>
                <!-- ... (resto de las tablas HTML se mantienen igual) ... -->
                 <h2>Información curatorial</h2>

                <table id="tabla-geografica" class="table table-striped table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>Término</th>
                            <th>Información asignada por CONABIO</th>
                            <th>Información original</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Procedencia del ejemplar</td>
                            <td><?php echo '';  ?></td>
                            <td><?php echo $procedenciaejemplar; ?></td>
                        </tr>
                        <tr>
                            <td>Colección</td>
                            <td><?php echo $col; ?></td>
                            <td><?php echo $col; ?></td>
                        </tr>
                        <tr>
                            <td>Institución</td>
                            <td><?php echo $ins; ?></td>
                            <td><?php echo $ins; ?></td>
                        </tr>
                        <tr>
                            <td>Número de catálogo</td>
                            <td><?php echo '';  ?></td>
                            <td><?php echo $numcatalogo; ?></td>
                        </tr>
                        <tr>
                            <td>Número de colecta u observación</td>
                            <td><?php echo ''; ?></td>
                            <td><?php echo $numcolecta; ?></td>
                        </tr>
                        <tr>
                            <td>Número de individuos</td>
                            <td><?php echo '';  ?></td>
                            <td><?php echo  $numeroindividuos; ?></td>
                        </tr>
                        <tr>
                            <td>Colector u observador</td>
                            <td><?php echo '';  ?></td>
                            <td><?php
                                $personaLimpia = isset($persona) ? trim($persona) : '';
                                $personaMinusculas = strtolower($personaLimpia);
                                if ($personaLimpia === '' || $personaMinusculas === 'no disponible') {
                                    echo isset($colector) ? htmlspecialchars($colector) : 'N/A';
                                } else {
                                    echo htmlspecialchars($persona);
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Fecha de colecta u observación</td>
                            <td><?php echo '';  ?></td>
                            <td><?php echo $fechacolecta;  ?></td>
                        </tr>
                        <tr>
                            <td>Determinador</td>
                            <td><?php echo '';  ?></td>
                            <td><?php
                                $personaLimpia = isset($persona) ? trim($persona) : '';
                                $personaMinusculas = strtolower($personaLimpia);
                                if ($personaLimpia === '' || $personaMinusculas === 'no disponible') {
                                    echo isset($determinador) ? htmlspecialchars($determinador) : 'N/A';
                                } else {
                                    echo htmlspecialchars($persona);
                                }
                                ?>

                            </td>
                        </tr>
                        <tr>
                            <td>Fecha de la determinación</td>
                            <td><?php echo '';  ?></td>
                            <td><?php echo $fechadeterminacion;  ?></td>
                        </tr>
                        <tr>
                            <td>Tipo de preparación</td>
                            <td><?php echo ''; ?></td>
                            <td><?php echo $tipoPreparacion  ?></td>
                        </tr>
                    </tbody>
                </table>


                <h2>Información taxonómica</h2>

                <table id="tabla-geografica" class="table table-striped table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>Término</th>
                            <th>Información asignada por CONABIO</th>
                            <th>Información original</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Reino</td>
                            <td><?php echo $reinocatvalido;  ?></td>
                            <td><?php echo $reinooriginal; ?></td>
                        </tr>
                        <tr>
                            <td>División o Phylum</td>
                            <td><?php echo $divisionphylumcatvalido;  ?></td>
                            <td><?php echo $divisionphylumoriginal; ?></td>
                        </tr>
                        <tr>
                            <td>Clase</td>
                            <td><?php echo $clasecatvalido;  ?></td>
                            <td><?php echo $claseoriginal; ?></td>
                        </tr>
                        <tr>
                            <td>Orden</td>
                            <td><?php echo $ordencatvalido;  ?></td>
                            <td><?php echo $ordenoriginal; ?></td>
                        </tr>
                        <tr>
                            <td>Familia</td>
                            <td><?php echo $familiacatvalido; ?></td>
                            <td><?php echo $familiaoriginal; ?></td>
                        </tr>
                        <tr>
                            <td>Género</td>
                            <td><?php echo $generocatvalido;  ?></td>
                            <td><?php echo $generooriginal; ?></td>
                        </tr>
                        <tr>
                            <td>Epíteto específico</td>
                            <td><?php echo $epitetoespecificocatvalido;  ?></td>
                            <td><?php echo $epitetoespecificooriginal; ?></td>
                        </tr>
                        <tr>
                            <td>Epíteto infraespecífico</td>
                            <td><?php echo $epitetoinfraespecificocatvalido;  ?></td>
                            <td><?php echo $epitetoinfraespecificooriginal;  ?></td>
                        </tr>
                        <tr>
                            <td>Categoria de la infraespecie</td>
                            <td><?php echo $categoriainfraespeciecatvalido;  ?></td>
                            <td><?php echo $categoriainfraespecieoriginal;  ?></td>
                        </tr>
                        <tr>
                            <td>Nombre científico</td>
                            <td><?php echo $nombrevalidocatscat;  ?></td>
                            <td><?php echo $nombreoriginallimpioscat;  ?></td>
                        </tr>

                        <tr>
                            <td>Sistema de clasificación/Catálogo de autoridad</td>
                            <td><?php if ($catdiccinfraespeciecat === '') {
                                    echo $catdiccespeciecat;
                                } else {
                                    echo $catdiccinfraespeciecat;
                                }
                                ?></td>
                            <td><?php if ($catdiccinfraespecieoriginal === '') {
                                    echo $catdiccespecieoriginal;
                                } else {
                                    echo $catdiccinfraespecieoriginal;
                                }  ?></td>
                        </tr>
                        <tr>
                            <td>Origen</td>
                            <td><?php echo $endemismo; ?></td>
                            <td><?php echo ''; ?></td>
                        </tr>
                        <tr>
                            <td>IUCN</td>
                            <td><?php echo $iucn;  ?></td>
                            <td><?php echo ''; ?></td>
                        </tr>
                        <tr>
                            <td>CITES</td>
                            <td><?php echo $cites;  ?></td>
                            <td><?php echo ''; ?></td>
                        </tr>
                        <tr>
                            <td>NOM-059</td>
                            <td><?php echo $nom059;  ?></td>
                            <td><?php echo '';  ?></td>
                        </tr>
                        <tr>
                            <td>Especie prioritaria</td>
                            <td><?php echo $prioritaria;  ?></td>
                            <td><?php echo '';  ?></td>
                        </tr>
                        <tr>
                            <td>Especie exotica / invasora</td>
                            <td><?php echo $exoticainvasora;  ?></td>
                            <td><?php echo '';  ?></td>
                        </tr>
                    </tbody>
                </table>



                <h2>Información geográfica</h2>

                <table id="tabla-geografica" class="table table-striped table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>Término</th>
                            <th>Información asignada por CONABIO</th>
                            <th>Información original</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>País</td>
                            <td><?php echo $paismapa;  ?></td>
                            <td><?php echo $paisoriginal; ?></td>
                        </tr>
                        <tr>
                            <td>Estado</td>
                            <td><?php echo $estadomapa;  ?></td>
                            <td><?php echo $estadooriginal; ?></td>
                        </tr>
                        <tr>
                            <td>Municipio</td>
                            <td><?php echo $municipiomapa;  ?></td>
                            <td><?php echo $municipiooriginal; ?></td>
                        </tr>
                        <tr>
                            <td>Localidad</td>
                            <td><?php echo '';  ?></td>
                            <td><?php echo $localidad; ?></td>
                        </tr>
                        <tr>
                            <td>Coordenadas geográficas</td>
                            <td><?php
                                if ($coordenadas_validas_para_mapa) {
                                    echo "Latitud " . htmlspecialchars($lat) . ", longitud " . htmlspecialchars($lon);
                                } else {
                                    echo "Sin coordenadas";
                                }
                                ?> </td>
                            <td><?php echo $coordenadaDescripcion; ?></td>
                        </tr>
                        <tr>
                            <td>Datum</td>
                            <td><?php echo $datum; ?></td>
                            <td><?php echo $datumoriginal;  ?></td>
                        </tr>
                        <tr>
                            <td>Metodo de obtención de la georreferencia</td>
                            <td><?php echo '';  ?></td>
                            <td><?php echo $geoposmapagacetlitetiq;  ?></td>
                        </tr>
                        <tr>
                            <td>Fuente georreferencia</td>
                            <td><?php echo '';  ?></td>
                            <td><?php echo $fuentegeorreferenciacion;  ?></td>
                        </tr>
                        <tr>
                            <td>Tipo de vegetación</td>
                            <td><?php echo $tipovegetacionmapa;  ?></td>
                            <td><?php echo $tipovegetacion;  ?></td>
                        </tr>
                        <tr>
                            <td>Altitud o porfundidad</td>
                            <td><?php echo $altitudmapa; ?></td>
                            <td><?php echo $altitudinicialejemplar;  ?></td>
                        </tr>
                    </tbody>
                </table>


                <h2>Datos del recurso</h2>
                <table id="tabla-recurso" class="table table-striped table-hover table-bordered">
                    <tbody>
                        <tr>
                            <td>Observaciones sobre la información del ejemplar</td>
                            <td><?php echo $obsusoinfo;  ?></td>
                        </tr>
                        <tr>
                            <td>Url del ejemplar</td>
                            <td><a href="<?php echo $urlejemplar; ?>" target="_blank"><?php echo $urlejemplar; ?></a></td>
                        </tr>
                        <tr>
                            <td>Fecha de actualización</td>
                            <td><?php echo $ultimafechaactualizacion; ?></td>
                        </tr>
                        <tr>
                            <td>Licencia de uso</td>
                            <td><?php echo $licenciauso; ?></td>
                        </tr>
                        <tr>
                            <td>Forma de citar</td>
                            <td><?php echo $formadecitar; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-8226401-20"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Leaflet.awesome-markers/2.0.2/leaflet.awesome-markers.min.js"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'UA-8226401-20');

        var globalMapInstance = null;
        var globalInfoDivMarker = null;

        
        function closeGlobalInfoDivMarker() {
            console.log("closeGlobalInfoDivMarker called");
            if (globalInfoDivMarker && globalMapInstance) {
                if (globalMapInstance.hasLayer(globalInfoDivMarker)) {
                    globalMapInstance.removeLayer(globalInfoDivMarker);
                    console.log("GlobalInfoDivMarker removed from map");
                }
                globalInfoDivMarker = null; 
            } else {
                console.log("No globalInfoDivMarker or globalMapInstance to remove.");
            }
            return false; 
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var coordenadasValidasDesdePHP = <?php echo json_encode($coordenadas_validas_para_mapa); ?>;
            var latPHP = <?php echo json_encode($lat); ?>; 
            var lonPHP = <?php echo json_encode($lon); ?>; 
            var incertidumbrePHP = <?php echo json_encode($incertidumbreXY, JSON_NUMERIC_CHECK); ?>;
            var incertidumbre = (incertidumbrePHP === null || incertidumbrePHP === 0) ? null : incertidumbrePHP;
            var observacionesCoordenadasConabioJS = <?php echo json_encode($observacionescoordenadasconabio); ?>;

            const umbralIncertidumbre = 50000; 

            if (!coordenadasValidasDesdePHP) {
                console.error("Coordenadas inválidas (según PHP). No se inicializará el mapa.");
                return;
            }
            
            const lat = parseFloat(latPHP);
            const lon = parseFloat(lonPHP);

            if (isNaN(lat) || isNaN(lon)) {
                console.error("Coordenadas inválidas para el mapa: ", latPHP, lonPHP);
                document.getElementById('map').innerHTML = '<p style="color: red; text-align: center;">Error: No se pueden mostrar las coordenadas en el mapa.</p>';
                return;
            }

            var map = L.map('map', {
                minZoom: 1
            }).setView([lat, lon], 8);
            globalMapInstance = map; 
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            L.control.scale({
                metric: true,
                imperial: false
            }).addTo(map);

            const iconoAwesomeAzul = L.AwesomeMarkers.icon({
                icon: 'circle',
                prefix: 'fa',
                markerColor: 'blue',
                iconSize: [35, 45], 
                iconAnchor: [17, 42], 
                popupAnchor: [1, -34]  
            });
            const iconoAwesomeRojo = L.AwesomeMarkers.icon({
                icon: 'circle',
                prefix: 'fa',
                markerColor: 'red',
                iconSize: [35, 45],
                iconAnchor: [17, 42],
                popupAnchor: [1, -34] 
            });

            var mainMarker = L.marker([lat, lon], { 
                icon: iconoAwesomeAzul 
            }).addTo(map);

            var incertidumbreNumerica = (incertidumbre !== null) ? parseFloat(incertidumbre) : NaN;
            let contenidoPopup = '';
            let opcionesCirculo = {};
            let debeMostrarCirculo = false;
            let debeAjustarBounds = false;
            var circle = null;
            
            function clearLocalInfoDivMarker() { 
                console.log("clearLocalInfoDivMarker called");
                if (globalInfoDivMarker && globalMapInstance) {
                    if (globalMapInstance.hasLayer(globalInfoDivMarker)) {
                        globalMapInstance.removeLayer(globalInfoDivMarker);
                    }
                    globalInfoDivMarker = null;
                }
            }


            if (!isNaN(incertidumbreNumerica) && incertidumbreNumerica > 0) {
                debeMostrarCirculo = true;
                debeAjustarBounds = true;
                let iconoParaUsar = iconoAwesomeAzul;

                if (incertidumbreNumerica > umbralIncertidumbre) {

                    /* const esGeorreferenciadoConabioInbio = 
                        observacionesCoordenadasConabioJS === 'Georreferenciado en la Conabio' ||
                        observacionesCoordenadasConabioJS === 'Georreferenciado en el INBIO';

                    if (esGeorreferenciadoConabioInbio) { 
                        iconoParaUsar = iconoAwesomeAzul;
                        opcionesCirculo = {
                            radius: incertidumbreNumerica,
                            color: '#3388ff', 
                            fillColor: '#3388ff',
                            fillOpacity: 0.2
                        };
                        contenidoPopup = `
                            <div><strong>Incertidumbre geográfica:</strong> ${incertidumbreNumerica.toLocaleString()} m</div>
                            <div class="popup-warning">
                                <span class="warning-icon fas fa-exclamation-triangle"></span>
                                <strong>Posible inconsistencia</strong>
                            </div>
                        `;
                        clearLocalInfoDivMarker(); 
                    } else {  */
                        iconoParaUsar = iconoAwesomeRojo;
                        opcionesCirculo = {
                            radius: incertidumbreNumerica,
                            color: '#ff0000', 
                            fillColor: '#ff0000',
                            fillOpacity: 0.2
                        };
                        
                        clearLocalInfoDivMarker(); 
                        
                        const divIconText = `Incertidumbre geográfica: ${incertidumbreNumerica.toLocaleString()} m`;
                        const divIconHTML = `
                            <a href="#" onclick="return closeGlobalInfoDivMarker();" class="custom-info-divicon-close" title="Cerrar">×</a>
                            ${divIconText}
                        `;

                        const customDivIcon = L.divIcon({
                            className: 'custom-info-divicon',
                            html: divIconHTML,
                            iconSize: [300, 45], // Ajustar [ancho, alto] según el contenido y padding
                                                 // El alto debe ser suficiente para el texto y el botón de cerrar
                            iconAnchor: [150, 90] // [ancho/2, alto_icono + offset_deseado_hacia_arriba]
                                                 // Si alto_icono es 35, y queremos 15px arriba de la punta del marker: 35+15=50
                        });
                        
                        globalInfoDivMarker = L.marker([lat, lon], { 
                            icon: customDivIcon, 
                            zIndexOffset: 1000
                        }).addTo(map); 
                        console.log("GlobalInfoDivMarker created and added to map");
                        
                        contenidoPopup = ''; 
                    /* } */
                } else {
                    iconoParaUsar = iconoAwesomeAzul;
                    opcionesCirculo = {
                        radius: incertidumbreNumerica,
                        color: '#3388ff',
                        fillColor: '#3388ff',
                        fillOpacity: 0.2
                    };
                    contenidoPopup = `
                        <div><strong>Incertidumbre geográfica:</strong> ${incertidumbreNumerica.toLocaleString()} m</div>
                    `;
                    clearLocalInfoDivMarker();
                }

                mainMarker.setIcon(iconoParaUsar);

                if (debeMostrarCirculo) {
                    circle = L.circle([lat, lon], opcionesCirculo).addTo(map);
                }

            } else { 
                const textoIncertidumbreMostrar = "No proporcionada";
                debeMostrarCirculo = false;
                contenidoPopup = `
                    <div><strong>Incertidumbre geográfica:</strong> ${textoIncertidumbreMostrar}</div>
                `;
                clearLocalInfoDivMarker();
            }

            if (contenidoPopup) {
                mainMarker.bindPopup(contenidoPopup);
                if (!globalInfoDivMarker) { // Usar la variable global para la condición
                    mainMarker.openPopup();
                }
            } else if (mainMarker.getPopup()) { 
                mainMarker.unbindPopup();
            }

            if (debeAjustarBounds && circle) {
                map.fitBounds(circle.getBounds(), {
                    padding: [50, 50]
                });
            } else {
                map.setView([lat, lon], 12);
            }
        });
    </script>
</body>
</html>