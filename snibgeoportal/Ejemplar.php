<!DOCTYPE html>
<html lang="es">

<head>
    <?php
    include("../includes/head-tag-contents.php");
    include('connectdb.php');
    require 'functions.php';

    $llaveejemplar = isset($_GET['id']) ? $_GET['id'] : '';


    //VALIDACION DE ESTADOREGISTRO
    $mensaje_alerta = '';

    if (strlen($llaveejemplar) != 32) {
        $mensaje_alerta = '<div class="alert alert-danger" role="alert" >' .
            '<b>El identificador ' . htmlspecialchars($llaveejemplar) . ', no es un identificador válido para el SNIB.</b>' .
            '</div>';
    } else {
        mysqli_next_result($mysqli);
        if ($stmt = $mysqli->prepare("SELECT e.llaveejemplar, e.actualizadopor, e.estadoregistro FROM snib.ejemplar_curatorial e where llaveejemplar = ?")) {
            $stmt->bind_param("s", $llaveejemplar);
            $stmt->execute();
            $result = $stmt->get_result();
            $datosEjemplar = $result->fetch_array(MYSQLI_NUM);
            $conteosEjemplares = $result->num_rows;
            $stmt->close();

            if ($conteosEjemplares == 0) {
                $mensaje_alerta = '<div class="alert alert-danger" role="alert" >' .
                    '<b>El identificador ' . htmlspecialchars($llaveejemplar) . ', no es un identificador perteneciente al SNIB.</b>' .
                    '</div>';
            } else {
                if ($datosEjemplar[2] != "" and $datosEjemplar[1] == "") {
                    $mensaje_alerta = '<div class="alert alert-danger" role="alert" >' .
                        '<b>El identificador ' . htmlspecialchars($llaveejemplar) . ' ya no se encuentra activo en la base del SNIB por el siguiente motivo: ' . htmlspecialchars($datosEjemplar[2]) . '.</b>' .
                        '</div>';
                } else if ($datosEjemplar[1] != "") {
                    $mensaje_alerta = '<div class="alert alert-warning" role="alert" >' .
                        '<b>El identificador proporcionado (' . htmlspecialchars($llaveejemplar) . ') fue actualizado en la base del SNIB por el ejemplar con identificador ' . htmlspecialchars($datosEjemplar[1]) . '. A continuación se presenta la información del ejemplar con el nuevo identificador.</b>' .
                        '</div>';
                    $llaveejemplar = $datosEjemplar[1];
                }
            }
        }
    }



    $enciclovida = new enciclovida();
    $urlComments = $enciclovida->ligaComentarios($mysqli, $llaveejemplar);
    if (strpos($mensaje_alerta, 'alert-danger') === false) {
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
            $catdiccespeciecatvalido,
            $catdiccinfraespeciecatvalido,
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
            $observacionescoordenadasconabio,
            $urlorigen,
            $tipositio

        ) = $enciclovida->obtenResumen($mysqli, $llaveejemplar);
        $titulo = $enciclovida->obtenProyecto($mysqli, $llaveejemplar);
        $mysqli->close();
        $coordenadas_validas_para_mapa = (isset($lat) && is_numeric($lat) && isset($lon) && is_numeric($lon));
    } else {
        $coordenadas_validas_para_mapa = false;
    }

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Leaflet.awesome-markers/2.0.2/leaflet.awesome-markers.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
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

        .warning-icon {
            font-size: 1.9em;
            margin-right: 5px;
            line-height: 1;
        }

        .custom-info-divicon {
            background: white;
            padding: 10px 15px;
            padding-right: 30px;
            border-radius: 6px;
            box-shadow: 0 2px 7px rgba(0, 0, 0, 0.45);
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

        #floating-download-btn {
            position: fixed;
            right: 30px;
            top: 90px;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s;
            border-radius: 30px;
        }

        #floating-download-btn:hover {
            transform: scale(1.05);
        }

        #map {
            height: 400px;
            width: 100%;
            margin-bottom: 0;
            padding: 0;
            border-bottom: 1px solid #ccc;
        }

        #coordinate-container {
            height: 30px;
            width: 100%;
            background-color: #9B2247;
            color: #FFFFFF;
            display: flex;
            align-items: center;
            padding: 0 15px;
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin-top: 0;
            box-sizing: border-box;
            border-top: 1px solid rgba(255, 255, 255, 0.3);
        }

        .coordinate-group {
            margin: 0 15px;
        }

        .coordinate-label {
            font-weight: bold;
            color: #E6D194;
            margin-right: 8px;
            opacity: 0.9;
        }

        .coordinate-value {
            font-family: monospace;
            color: #FFFFFF;
        }

        .conabio-attribution-logo {
            height: 15px;
            width: auto;
            vertical-align: middle;
            margin-right: 1px;
        }
    </style>
</head>

<body>
    <?php
    include("../includes/navigation.php");
    ?>


    <button id="floating-download-btn" class="btn" style="background-color: #9B2247; color: white;">Descargar información (csv)
        <i class="fa fa-download"></i>
    </button>

    <div class="container" style="padding-top:15px;">

        <?php
        if (!empty($mensaje_alerta)) {
            echo $mensaje_alerta;
            if (strpos($mensaje_alerta, 'alert-danger') !== false) {
                echo '</div></body></html>';
                exit;
            }
        }
        ?>

        <div class="card">
            <div style="background-color: #9B2247; color:rgb(255, 255, 255); " class="card-header">
                <h2 style="text-align:center; margin-top: 0; margin-bottom: 0;"><i><?php echo $nombrevalidocatscat; ?></i> <?php echo $autor; ?></h2>
            </div>
            <div class="card-body">
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

                        <?php if (tieneDatoSignificativo($region)) : ?>
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
                    <div id="coordinate-container">
                        <div class="coordinate-group">
                            <span class="coordinate-label">Decimal:</span>
                            <span id="decimal-coords" class="coordinate-value">0.000000, 0.000000</span>
                        </div>
                        <div class="coordinate-group">
                            <span class="coordinate-label">GMS:</span>
                            <span id="dms-coords" class="coordinate-value">0°0'0" N, 0°0'0" E</span>
                        </div>
                    </div>
                </div>
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
                        <tr>
                            <td>Tipo de vegetación</td>
                            <td></td>
                            <td><?php echo $tipovegetacion;  ?></td>
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
                            <td><?php if ($catdiccinfraespeciecatvalido === '') {
                                    echo $catdiccespeciecatvalido;
                                } else {
                                    echo $catdiccinfraespeciecatvalido;
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
                            <td>Método de obtención de la georreferencia</td>
                            <td><?php echo '';  ?></td>
                            <td><?php echo $geoposmapagacetlitetiq;  ?></td>
                        </tr>
                        <tr>
                            <td>Fuente georreferencia</td>
                            <td><?php echo '';  ?></td>
                            <td><?php echo $fuentegeorreferenciacion;  ?></td>
                        </tr>                        
                        <tr>
                            <td>Altitud o profundidad</td>
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
                            <td>Url de origen</td>
                            <td><a href="<?php echo $urlorigen; ?>" target="_blank"><?php echo $urlorigen; ?></a></td>
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

            //ES PARA QUITAR EL Leaflet
           /*  map.attributionControl.setPrefix('');  */

            globalMapInstance = map;

            const conabioLogoUrl = '../images/logo_conabio.png';

            const customAttribution = `
            <img src="${conabioLogoUrl}" class="conabio-attribution-logo" >
            CONABIO 
            `;

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: customAttribution
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


            function mostrarDivIncertidumbre() {
                if (globalInfoDivMarker) {
                    return;
                }
                let textoIncertidumbre;
                if (!isNaN(incertidumbreNumerica) && incertidumbreNumerica > 0) {
                    textoIncertidumbre = `Incertidumbre geográfica: ${incertidumbreNumerica.toLocaleString()} m`;
                } else {
                    textoIncertidumbre = `Incertidumbre geográfica: No proporcionada`;
                }
                const divIconHTML = `
                    <a href="#" onclick="closeGlobalInfoDivMarker(); return false;" class="custom-info-divicon-close" title="Cerrar">×</a>
                    ${textoIncertidumbre}
                `;
                const customDivIcon = L.divIcon({
                    className: 'custom-info-divicon',
                    html: divIconHTML,
                    iconSize: [300, 45],
                    iconAnchor: [150, 90]
                });
                globalInfoDivMarker = L.marker([lat, lon], {
                    icon: customDivIcon,
                    zIndexOffset: 1000
                }).addTo(map);
            }


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
                    iconoParaUsar = iconoAwesomeAzul;
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
                        iconSize: [300, 45],
                        iconAnchor: [150, 90]
                    });

                    globalInfoDivMarker = L.marker([lat, lon], {
                        icon: customDivIcon,
                        zIndexOffset: 1000
                    }).addTo(map);
                    console.log("GlobalInfoDivMarker created and added to map");

                    contenidoPopup = '';
                } else {
                    iconoParaUsar = iconoAwesomeAzul;
                    opcionesCirculo = {
                        radius: incertidumbreNumerica,
                        color: '#3388ff',
                        fillColor: '#3388ff',
                        fillOpacity: 0.2
                    };

                    clearLocalInfoDivMarker();

                    const divIconText = `Incertidumbre geográfica: ${incertidumbreNumerica.toLocaleString()} m`;
                    const divIconHTML = `
                            <a href="#" onclick="closeGlobalInfoDivMarker(); return false;" class="custom-info-divicon-close" title="Cerrar">×</a>
                            ${divIconText}
                        `;

                    const customDivIcon = L.divIcon({
                        className: 'custom-info-divicon',
                        html: divIconHTML,
                        iconSize: [260, 45],
                        iconAnchor: [130, 90]
                    });

                    globalInfoDivMarker = L.marker([lat, lon], {
                        icon: customDivIcon,
                        zIndexOffset: 1000
                    }).addTo(map);

                    contenidoPopup = '';
                }
                mostrarDivIncertidumbre();

                mainMarker.on('click', function() {
                    mostrarDivIncertidumbre();
                });

                function toDMS(decimal, isLat) {
                    var dir = decimal < 0 ? (isLat ? 'S' : 'W') : (isLat ? 'N' : 'E');
                    var absDecimal = Math.abs(decimal);
                    var deg = Math.floor(absDecimal);
                    var minFloat = (absDecimal - deg) * 60;
                    var min = Math.floor(minFloat);
                    var sec = Math.round((minFloat - min) * 60 * 100) / 100; // Redondeamos a 2 decimales

                    // Asegurar que los segundos no sean 60 (podría ocurrir por redondeo)
                    if (sec >= 60) {
                        sec = 0;
                        min++;
                    }
                    if (min >= 60) {
                        min = 0;
                        deg++;
                    }

                    return deg + "°" + min + "'" + sec + '" ' + dir;
                }

                map.on('mousemove', function(e) {
                    var lat = e.latlng.lat;
                    var lng = e.latlng.lng;

                    // Actualizar coordenadas decimales
                    document.getElementById('decimal-coords').textContent =
                        lat.toFixed(6) + ', ' + lng.toFixed(6);

                    // Actualizar coordenadas GMS
                    document.getElementById('dms-coords').textContent =
                        toDMS(lat, true) + ', ' + toDMS(lng, false);
                });

                mainMarker.setIcon(iconoParaUsar);

                if (debeMostrarCirculo) {
                    circle = L.circle([lat, lon], opcionesCirculo).addTo(map);
                }

            } else {
                debeMostrarCirculo = false;
                clearLocalInfoDivMarker();
                const divIconText = `Incertidumbre geográfica: No proporcionada`;
                const divIconHTML = `
                        <a href="#" onclick="closeGlobalInfoDivMarker(); return false;" class="custom-info-divicon-close" title="Cerrar">×</a>
                        ${divIconText}
                    `;

                const customDivIcon = L.divIcon({
                    className: 'custom-info-divicon',
                    html: divIconHTML,
                    iconSize: [300, 45],
                    iconAnchor: [150, 90]
                });

                globalInfoDivMarker = L.marker([lat, lon], {
                    icon: customDivIcon,
                    zIndexOffset: 1000
                }).addTo(map);

                contenidoPopup = '';

                mostrarDivIncertidumbre();

                mainMarker.on('click', function() {
                    mostrarDivIncertidumbre();
                });

            }

            if (contenidoPopup) {
                mainMarker.bindPopup(contenidoPopup);
                if (!globalInfoDivMarker) {
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






        const botonDescarga = document.getElementById('floating-download-btn');

        <?php if (!empty($mensaje_alerta) && strpos($mensaje_alerta, 'alert-danger') !== false) : ?>
            if (botonDescarga) {
                botonDescarga.style.display = 'none';
            }
        <?php else: ?>

            <?php
            $ubicacionCompleta = '';
            if (isset($region) && tieneDatoSignificativo($region)) {
                $ubicacionCompleta = $region;
                if (isset($localidad) && tieneDatoSignificativo($localidad)) {
                    $ubicacionCompleta .= " / " . $localidad;
                }
            }


            $catalogoConabio = '';
            if (isset($catdiccinfraespeciecat) && $catdiccinfraespeciecat !== '') {
                $catalogoConabio = $catdiccinfraespeciecat;
            } elseif (isset($catdiccespeciecatvalido)) {
                $catalogoConabio = $catdiccespeciecatvalido;
            }

            $catalogoOriginal = '';
            if (isset($catdiccinfraespecieoriginal) && $catdiccinfraespecieoriginal !== '') {
                $catalogoOriginal = $catdiccinfraespecieoriginal;
            } elseif (isset($catdiccespecieoriginal)) {
                $catalogoOriginal = $catdiccespecieoriginal;
            }
            ?>


            if (botonDescarga) {
                const todosLosDatos = {
                    "llaveEjemplar": <?php echo json_encode($llaveejemplar ?? null); ?>,
                    "nombreComun": <?php echo json_encode($commonName ?? null); ?>,
                    "ubicacion": <?php echo json_encode($ubicacionCompleta ?? null); ?>,
                    "latitud": <?php echo json_encode($lat ?? null); ?>,
                    "longitud": <?php echo json_encode($lon ?? null); ?>,
                    "proyecto": <?php echo json_encode(strip_tags($titulo ?? '') ?? null); ?>,
                    "procedenciaEjemplar": <?php echo json_encode(null); ?>,
                    "procedenciaEjemplarOriginal": <?php echo json_encode($procedenciaejemplar ?? null); ?>,
                    "coleccion": <?php echo json_encode($col ?? null); ?>,
                    "coleccionOriginal": <?php echo json_encode($col ?? null); ?>,
                    "institucion": <?php echo json_encode($ins ?? null); ?>,
                    "institucionOriginal": <?php echo json_encode($ins ?? null); ?>,
                    "numeroCatalogo": <?php echo json_encode(null); ?>,
                    "numeroCatalogoOriginal": <?php echo json_encode($numcatalogo ?? null); ?>,
                    "numeroColecta": <?php echo json_encode(null); ?>,
                    "numeroColectaOriginal": <?php echo json_encode($numcolecta ?? null); ?>,
                    "numeroIndividuos": <?php echo json_encode(null); ?>,
                    "numeroIndividuosOriginal": <?php echo json_encode($numeroindividuos ?? null); ?>,
                    "colector": <?php echo json_encode(null); ?>,
                    "colectorOriginal": <?php echo json_encode($colector ?? null); ?>,
                    "fechaColecta": <?php echo json_encode(null); ?>,
                    "fechaColectaOriginal": <?php echo json_encode($fechacolecta ?? null); ?>,
                    "determinador": <?php echo json_encode(null); ?>,
                    "determinadorOriginal": <?php echo json_encode($determinador ?? null); ?>,
                    "fechaDeterminacion": <?php echo json_encode(null); ?>,
                    "fechaDeterminacionOriginal": <?php echo json_encode($fechadeterminacion ?? null); ?>,
                    "tipoPreparacion": <?php echo json_encode(null); ?>,
                    "tipoPreparacionOriginal": <?php echo json_encode($tipoPreparacion ?? null); ?>,
                    "reino": <?php echo json_encode($reinocatvalido ?? null); ?>,
                    "reinoOriginal": <?php echo json_encode($reinooriginal ?? null); ?>,
                    "phylum": <?php echo json_encode($divisionphylumcatvalido ?? null); ?>,
                    "phylumOriginal": <?php echo json_encode($divisionphylumoriginal ?? null); ?>,
                    "clase": <?php echo json_encode($clasecatvalido ?? null); ?>,
                    "claseOriginal": <?php echo json_encode($claseoriginal ?? null); ?>,
                    "orden": <?php echo json_encode($ordencatvalido ?? null); ?>,
                    "ordenOriginal": <?php echo json_encode($ordenoriginal ?? null); ?>,
                    "familia": <?php echo json_encode($familiacatvalido ?? null); ?>,
                    "familiaOriginal": <?php echo json_encode($familiaoriginal ?? null); ?>,
                    "genero": <?php echo json_encode($generocatvalido ?? null); ?>,
                    "generoOriginal": <?php echo json_encode($generooriginal ?? null); ?>,
                    "epitetoEspecifico": <?php echo json_encode($epitetoespecificocatvalido ?? null); ?>,
                    "epitetoEspecificoOriginal": <?php echo json_encode($epitetoespecificooriginal ?? null); ?>,
                    "epitetoInfraespecifico": <?php echo json_encode($epitetoinfraespecificocatvalido ?? null); ?>,
                    "epitetoInfraespecificoOriginal": <?php echo json_encode($epitetoinfraespecificooriginal ?? null); ?>,
                    "categoriaInfraespecie": <?php echo json_encode($categoriainfraespeciecatvalido ?? null); ?>,
                    "categoriaInfraespecieOriginal": <?php echo json_encode($categoriainfraespecieoriginal ?? null); ?>,
                    "nombreCientifico": <?php echo json_encode($nombrevalidocatscat ?? null); ?>,
                    "nombreCientificoOriginal": <?php echo json_encode($nombreoriginallimpioscat ?? null); ?>,
                    "nombreCientificoOriginal": <?php echo json_encode($nombreoriginallimpioscat ?? null); ?>,
                    "catalogoAutoridad": <?php echo json_encode($catalogoConabio ?? null); ?>,
                    "catalogoAutoridadOriginal": <?php echo json_encode($catalogoOriginal ?? null); ?>,
                    "origen": <?php echo json_encode($endemismo ?? null); ?>,
                    "origenOriginal": <?php echo json_encode(null); ?>,
                    "iucn": <?php echo json_encode($iucn ?? null); ?>,
                    "iucnOriginal": <?php echo json_encode(null); ?>,
                    "cites": <?php echo json_encode($cites ?? null); ?>,
                    "citesOriginal": <?php echo json_encode(null); ?>,
                    "nom059": <?php echo json_encode($nom059 ?? null); ?>,
                    "nom059Original": <?php echo json_encode(null); ?>,
                    "especiePrioritaria": <?php echo json_encode($prioritaria ?? null); ?>,
                    "especiePrioritariaOriginal": <?php echo json_encode(null); ?>,
                    "especieInvasora": <?php echo json_encode($exoticainvasora ?? null); ?>,
                    "especieInvasoraOriginal": <?php echo json_encode(null); ?>,
                    "pais": <?php echo json_encode($paismapa ?? null); ?>,
                    "paisOriginal": <?php echo json_encode($paisoriginal ?? null); ?>,
                    "estado": <?php echo json_encode($estadomapa ?? null); ?>,
                    "estadoOriginal": <?php echo json_encode($estadooriginal ?? null); ?>,
                    "municipio": <?php echo json_encode($municipiomapa ?? null); ?>,
                    "municipioOriginal": <?php echo json_encode($municipiooriginal ?? null); ?>,
                    "localidad": <?php echo json_encode(null); ?>,
                    "localidadOriginal": <?php echo json_encode($localidad ?? null); ?>,
                    "datum": <?php echo json_encode($datum ?? null); ?>,
                    "datumOriginal": <?php echo json_encode($datumoriginal ?? null); ?>,
                    "metodoObtencionGeorreferencia": <?php echo json_encode(null); ?>,
                    "metodoObtencionGeorreferenciaOriginal": <?php echo json_encode($geoposmapagacetlitetiq ?? null); ?>,
                    "fuenteGeorreferenciacion": <?php echo json_encode(null); ?>,
                    "fuenteGeorreferenciacionOriginal": <?php echo json_encode($fuentegeorreferenciacion ?? null); ?>,
                    "tipoVegetacion": <?php echo json_encode($tipovegetacionmapa ?? null); ?>,
                    "tipoVegetacionOriginal": <?php echo json_encode($tipovegetacion ?? null); ?>,
                    "altitud": <?php echo json_encode($altitudmapa ?? null); ?>,
                    "altitudOriginal": <?php echo json_encode($altitudinicialejemplar ?? null); ?>,
                    "observaciones": <?php echo json_encode($obsusoinfo ?? null); ?>,
                    "URL_del_ejemplar": <?php echo json_encode($urlejemplar ?? null); ?>,
                    "URL_de_origen": <?php echo json_encode($urlorigen ?? null); ?>,
                    "fechaActualizacion": <?php echo json_encode($ultimafechaactualizacion ?? null); ?>,
                    "licencia_de_uso": <?php echo json_encode($licenciauso ?? null); ?>,
                    "forma_de_citar": <?php echo json_encode($formadecitar ?? null); ?>,

                };

                function convertirObjetoA_CSV_Estricto(datos) {
                    const escaparCSV = (valor) => {
                        let str = String(valor ?? "");
                        str = str.replace(/"/g, '""');
                        return `"${str}"`;
                    };
                    const encabezados = Object.keys(datos).map(key => escaparCSV(key));
                    const valores = Object.values(datos).map(val => escaparCSV(val));
                    return [encabezados.join(','), valores.join(',')].join('\n');
                }

                function descargarCSV(csvContenido, nombreArchivo) {
                    const blob = new Blob(["\uFEFF" + csvContenido], {
                        type: 'text/csv;charset=utf-8;'
                    });
                    const link = document.createElement("a");
                    if (link.download !== undefined) {
                        const url = URL.createObjectURL(blob);
                        link.setAttribute("href", url);
                        link.setAttribute("download", nombreArchivo);
                        link.style.visibility = 'hidden';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        URL.revokeObjectURL(url);
                    }
                }

                botonDescarga.addEventListener('click', function() {
                    const contenidoCSV = convertirObjetoA_CSV_Estricto(todosLosDatos);
                    const nombreArchivo = `Ejemplar_${todosLosDatos["llaveEjemplar"] || 'sin_id'}.csv`;
                    descargarCSV(contenidoCSV, nombreArchivo);
                });
            }
        <?php endif; ?>
    </script>



</body>

</html>