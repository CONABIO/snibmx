<?php
class enciclovida
{
    function ligaComentarios($mysqli, $llaveejemplar): string
    {
        $one = array("ECHIN", "ANIM");
        $two = array("CRUST");
        $three = array("FUNGI");
        $four = array("ANNEL", "BRACH", "BRYOZ", "CNIDA", "CTENO", "CHAET", "MOLUS", "MYXOZ", "ONYCH", "PHORO", "PLACO", "PORIF", "ROTIF", "SIPUN", "TARDI", "UROCH");
        $five = array("PLANT", "ANGIO", "BRIOF", "GIMNO", "PTERI", "VASCU");
        $six = array("PROT", "PROKA");
        $seven = array("AVES", "CONA", "REPTI", "ANFIB", "MAMIF", "PECES");
        $eight = array("DIPTE");
        $nine = array("ARACH", "ARTHR", "COLEO", "HEMIP", "HYMEN", "INSEC", "LEPID", "MYRIA", "ORTHO");

        $sql = "SELECT idnombre
                FROM snib.ejemplar_curatorial
                INNER JOIN snib.nombre_taxonomia USING(llavenombre)
                WHERE llaveejemplar = ?"; 

        $idnombre = null; 
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("s", $llaveejemplar); 
            $stmt->execute();
            $stmt->bind_result($idnombre); 
            $stmt->fetch();
            $stmt->close();
        } else {
            
            return ''; 
        }

        if ($idnombre === null) {
            return ''; 
        }

        $catalogo = preg_replace('/[0-9]+/', '', $idnombre);
        $idcat = intval(preg_replace('/[^0-9]+/', '', $idnombre), 10);
        $cod = 0;

        if (in_array($catalogo, $one)) {
            $cod = 1000000;
        } else if (in_array($catalogo, $two)) {
            $cod = 2000000;
        } else if (in_array($catalogo, $three)) {
            $cod = 3000000;
        } else if (in_array($catalogo, $four)) {
            $cod = 4000000;
        } else if (in_array($catalogo, $five)) {
            $cod = 6000000; 
        } else if (in_array($catalogo, $six)) {
            $cod = 7000000;
        } else if (in_array($catalogo, $seven)) {
            $cod = 8000000;
        } else if (in_array($catalogo, $eight)) {
            $cod = 9000000;
        } else if (in_array($catalogo, $nine)) {
            $cod = 10000000;
        }

        $codNombre = $cod + $idcat;

        return "http://www.enciclovida.mx/especies/{$codNombre}/comentarios/new?proveedor_id={$llaveejemplar}&tipo_proveedor=6";
    }

    function obtenResumen($mysqli, $llaveejemplar): array
    {
        
        $scientificName = $autor = $commonName = $region = $localidad = $procedenciaejemplar = $col = $ins = $lat = $lon = $fechacolecta = $colector =
            $datum = $ultimafechaactualizacion = $urlejemplar = $licenciauso = $formadecitar = $reino = $phylumdivision = $clase = $orden = $familia = $genero =
            $categoriainfraespecie = $fechadeterminacion = $numcatalogo = $numcolecta = $determinador = $obsusoinfo = $tipoPreparacion = $numeroindividuos =
            $persona = $reinocatvalido = $divisionphylumcatvalido = $clasecatvalido = $ordencatvalido = $familiacatvalido = $generocatvalido =
            $epitetoespecificocatvalido = $categoriainfraespeciecatvalido = $epitetoinfraespecificocatvalido = $reinooriginal = $divisionphylumoriginal =
            $claseoriginal = $ordenoriginal = $familiaoriginal = $generooriginal = $epitetoespecificooriginal = $epitetoinfraespecificooriginal =
            $categoriainfraespecieoriginal = $nombrevalidocatscat = $nombreoriginallimpioscat = $categoriacatscat = $categoriaoriginalscat = $autoranioespeciecat =
            $autoranioinfraespeciecat = $autoranioespecieoriginal = $autoranioinfraespecieoriginal = $estatusespeciecat = $estatusinfraespeciecat =
            $estatusespecieoriginal = $estatusinfraespecieoriginal = $catdiccespeciecat = $catdiccinfraespeciecat = $catdiccespecieoriginal =
            $catdiccinfraespecieoriginal = $endemismo = $iucn = $cites = $nom059 = $prioritaria = $exoticainvasora = $paisoriginal = $estadooriginal =
            $municipiooriginal = $geoposmapagacetlitetiq = $usvserieVII = $altitudmapa = $altitudinicialejemplar = $paismapa = $estadomapa = $municipiomapa =
            $datumoriginal = $tipovegetacion = $fuentegeorreferenciacion = $tipovegetacionmapa = $observacionescoordenadasconabio = '';
        $coordenadaDescripcion = $incertidumbreXY = $urlorigen = $tipositio = '' ;


        try {
            $sql = "SELECT
                        especievalida, autorvalido, TRIM(BOTH ',' FROM REGEXP_REPLACE(nombrecomun, '[^,]*\\\\[[^)]*\\\\][^,]*,? ?', '')) as nombrecomun, -- Escapado para PHP
                        region, localidad, procedenciaejemplar, coleccion, institucion,
                        i.latitud, i.longitud, fechacolecta, colector,
                        i.datum,
                        i.ultimafechaactualizacion, i.urlejemplar, i.licenciauso, formadecitar,
                        reino, phylumdivision, clase, orden, familia, genero, categoriainfraespecie, fechadeterminacion,
                        numcatalogo, numcolecta, determinador,
                        obsusoinfo, tp.tipopreparacion, e.numeroindividuoscopias AS numeroindividuos,
                        p.persona,
                        n.reinocatvalido, n.divisionphylumcatvalido, n.clasecatvalido, n.ordencatvalido, n.familiacatvalido, n.generocatvalido,
                        n.epitetoespecificocatvalido, n.categoriainfraespeciecatvalido, n.epitetoinfraespecificocatvalido,
                        n.reinooriginal, n.divisionphylumoriginal, n.claseoriginal, n.ordenoriginal, n.familiaoriginal, n.generooriginal, n.epitetoespecificooriginal,
                        n.epitetoinfraespecificooriginal, n.categoriainfraespecieoriginal,
                        n.nombrevalidocatscat, n.nombreoriginallimpioscat, n.categoriacatscat, n.categoriaoriginalscat, n.autoranioespeciecat,
                        n.autoranioinfraespeciecat, n.autoranioespecieoriginal, n.autoranioinfraespecieoriginal,
                        n.estatusespeciecat, n.estatusinfraespeciecat, n.estatusespecieoriginal, n.estatusinfraespecieoriginal,
                        n.catdiccespeciecat, n.catdiccinfraespeciecat, n.catdiccespecieoriginal, n.catdiccinfraespecieoriginal,
                        i.endemismo, i.iucn, i.cites, i.nom059, i.prioritaria, i.exoticainvasora,
                        r.paisoriginal, r.estadooriginal, r.municipiooriginal,
                        g.geoposmapagacetlitetiq,
                        i.usvserieVII, i.altitudmapa, e.altitudinicialejemplar,
                        i.paismapa, i.estadomapa, i.municipiomapa,
                        g.datum AS datumoriginal,
                        t.tipovegetacion, f.fuentegeorreferenciacion,

                        CASE
                            WHEN c.observacionescoordenadasconabio LIKE 'Conversión de sexagesimal%' THEN
                                CONCAT(
                                    IF (((g.latitudgrados IS NOT NULL AND g.latitudgrados<>999) OR (g.latitudminutos IS NOT NULL AND g.latitudminutos<>99) OR (g.latitudsegundos IS NOT NULL AND g.latitudsegundos<>99)),
                                    CONCAT('latitud ',
                                        IF(g.latitudgrados IS NOT NULL AND g.latitudgrados<>999,CONCAT(g.latitudgrados,'°') ,''),
                                        IF(g.latitudminutos IS NOT NULL AND g.latitudminutos<>99,CONCAT(g.latitudminutos,\"'\"), -- Usar comilla simple en SQL
                                            IF(g.latitudminutos= 99 AND g.latitudgrados IS NOT NULL AND g.latitudgrados <> 999 AND g.latitudsegundos IS NOT NULL AND g.latitudsegundos <> 99,CONCAT('0', \"'\"),'')), -- Usar comilla simple en SQL
                                        IF(g.latitudsegundos IS NOT NULL AND g.latitudsegundos<>99,CONCAT(g.latitudsegundos,'\"'),''),' ',g.nortesur,', '),''),
                                    IF (((g.longitudgrados IS NOT NULL AND g.longitudgrados<>999) OR (g.longitudminutos IS NOT NULL AND g.longitudminutos<>99) OR (g.longitudsegundos IS NOT NULL AND g.longitudsegundos<>99)),
                                    CONCAT('longitud ',
                                        IF(g.longitudgrados IS NOT NULL AND g.longitudgrados<>999,CONCAT(g.longitudgrados,'°') ,''),
                                        IF(g.longitudminutos IS NOT NULL AND g.longitudminutos<>99,CONCAT(g.longitudminutos,\"'\"), -- Usar comilla simple en SQL
                                            IF(g.longitudminutos= 99 AND g.longitudgrados IS NOT NULL AND g.longitudgrados <> 999 AND g.longitudsegundos IS NOT NULL AND g.longitudsegundos <> 99,CONCAT('0', \"'\"),'')), -- Usar comilla simple en SQL
                                        IF(g.longitudsegundos IS NOT NULL AND g.longitudsegundos<>99,CONCAT(g.longitudsegundos,'\"'),''),' ',g.esteoeste),'')
                                )
                            WHEN c.observacionescoordenadasconabio LIKE 'Conversión de UTM%' THEN
                                CONCAT(
                                    IF(g.utm_latitud IS NOT NULL, CONCAT('latitud ', CAST(g.utm_latitud AS CHAR), ', '), ''),
                                    IF(g.utm_longitud IS NOT NULL, CONCAT('longitud ', CAST(g.utm_longitud AS CHAR)), ''),
                                    IF(g.utm_zona IS NOT NULL, CONCAT(' zona UTM ', CAST(g.utm_zona AS CHAR)), '')
                                )
                            WHEN c.observacionescoordenadasconabio LIKE 'Grados decimales%' THEN
                                IF(g.coordenadaoriginal IS NOT NULL,
                                    CONCAT('latitud ', SUBSTRING_INDEX(REPLACE(REPLACE(ST_AsText(g.coordenadaoriginal), 'POINT(', ''), ')', ''), ' ', -1),
                                           ', longitud ', SUBSTRING_INDEX(SUBSTRING_INDEX(REPLACE(REPLACE(ST_AsText(g.coordenadaoriginal), 'POINT(', ''), ')', ''), ' ', 1), ' ', -1)),
                                NULL)
                            WHEN c.observacionescoordenadasconabio LIKE 'Georreferenciado%' THEN
                                IF(g.coordenadaoriginal IS NOT NULL,
                                    CONCAT('latitud ', SUBSTRING_INDEX(REPLACE(REPLACE(ST_AsText(g.coordenadaoriginal), 'POINT(', ''), ')', ''), ' ', -1),
                                           ', longitud ', SUBSTRING_INDEX(SUBSTRING_INDEX(REPLACE(REPLACE(ST_AsText(g.coordenadaoriginal), 'POINT(', ''), ')', ''), ' ', 1), ' ', -1)),
                                NULL)
                            WHEN c.observacionescoordenadasconabio LIKE 'Coordenada Georrectificada%' THEN
                                CASE
                                    WHEN (g.latitudgrados IS NOT NULL AND g.latitudgrados <> 999) THEN
                                        CONCAT(
                                            IF (((g.latitudgrados IS NOT NULL AND g.latitudgrados<>999) OR (g.latitudminutos IS NOT NULL AND g.latitudminutos<>99) OR (g.latitudsegundos IS NOT NULL AND g.latitudsegundos<>99)),
                                            CONCAT('latitud ',
                                                IF(g.latitudgrados IS NOT NULL AND g.latitudgrados<>999,CONCAT(g.latitudgrados,'°') ,''),
                                                IF(g.latitudminutos IS NOT NULL AND g.latitudminutos<>99,CONCAT(g.latitudminutos,\"'\"), -- Usar comilla simple en SQL
                                                    IF(g.latitudminutos= 99 AND g.latitudgrados IS NOT NULL AND g.latitudgrados <> 999 AND g.latitudsegundos IS NOT NULL AND g.latitudsegundos <> 99,CONCAT('0', \"'\"),'')), -- Usar comilla simple en SQL
                                                IF(g.latitudsegundos IS NOT NULL AND g.latitudsegundos<>99,CONCAT(g.latitudsegundos,'\"'),''),' ',g.nortesur,', '),''),
                                            IF (((g.longitudgrados IS NOT NULL AND g.longitudgrados<>999) OR (g.longitudminutos IS NOT NULL AND g.longitudminutos<>99) OR (g.longitudsegundos IS NOT NULL AND g.longitudsegundos<>99)),
                                            CONCAT('longitud ',
                                                IF(g.longitudgrados IS NOT NULL AND g.longitudgrados<>999,CONCAT(g.longitudgrados,'°') ,''),
                                                IF(g.longitudminutos IS NOT NULL AND g.longitudminutos<>99,CONCAT(g.longitudminutos,\"'\"), -- Usar comilla simple en SQL
                                                    IF(g.longitudminutos= 99 AND g.longitudgrados IS NOT NULL AND g.longitudgrados <> 999 AND g.longitudsegundos IS NOT NULL AND g.longitudsegundos <> 99,CONCAT('0', \"'\"),'')), -- Usar comilla simple en SQL
                                                IF(g.longitudsegundos IS NOT NULL AND g.longitudsegundos<>99,CONCAT(g.longitudsegundos,'\"'),''),' ',g.esteoeste),'')
                                        )
                                    WHEN (((g.latitudgrados IS NULL) OR (g.latitudgrados = 999)) AND (g.coordenadaoriginal IS NULL) AND (g.utm_latitud IS NOT NULL)) THEN
                                         CONCAT(
                                             IF(g.utm_latitud IS NOT NULL, CONCAT('latitud ', CAST(g.utm_latitud AS CHAR), ', '), ''),
                                             IF(g.utm_longitud IS NOT NULL, CONCAT('longitud ', CAST(g.utm_longitud AS CHAR)), ''),
                                             IF(g.utm_zona IS NOT NULL, CONCAT(' zona UTM ', CAST(g.utm_zona AS CHAR)), '')
                                         )
                                    WHEN (((g.latitudgrados IS NULL) OR (g.latitudgrados = 999)) AND (g.utm_latitud IS NULL) AND (g.coordenadaoriginal IS NOT NULL)) THEN
                                         IF(g.coordenadaoriginal IS NOT NULL,
                                             CONCAT('latitud ', SUBSTRING_INDEX(REPLACE(REPLACE(ST_AsText(g.coordenadaoriginal), 'POINT(', ''), ')', ''), ' ', -1),
                                                    ', longitud ', SUBSTRING_INDEX(SUBSTRING_INDEX(REPLACE(REPLACE(ST_AsText(g.coordenadaoriginal), 'POINT(', ''), ')', ''), ' ', 1), ' ', -1)),
                                         NULL)
                                    ELSE NULL
                                END
                            ELSE NULL
                        END AS coordenada_descripcion,

                        v.tipovegetacionmapa, i.incertidumbreXY, o.observacionescoordenadasconabio, i.urlorigen, g.tipositio
                    FROM snib.informaciongeoportal_siya i
                        INNER JOIN snib.ejemplar_curatorial e ON i.idejemplar = e.llaveejemplar
                        INNER JOIN snib.tipopreparacion tp USING(idtipopreparacion)
                        INNER JOIN snib.persona p ON p.idpersona = e.idnombrecolector
                        INNER JOIN snib.nombre n ON e.llavenombre = n.llavenombre
                        INNER JOIN snib.conabiogeografia cg using(llaveregionsitiosig)
                        INNER JOIN snib.fuentegeorreferenciacion f using(idfuentegeorreferenciacion)
                        INNER JOIN snib.geografiaoriginal g using(llavesitio)
                        INNER JOIN snib.vegetacionprimariainegi v USING(idvegetacionprimariainegi)
                        INNER JOIN snib.regionoriginal r using(idregionoriginal)
                        INNER JOIN snib.tipovegetacion t ON e.idtipovegetacion = t.idtipovegetacion
                        INNER JOIN snib.v_conabioGeografia c ON cg.llaveregionsitiosig = c.llaveregionsitiosig
                        INNER JOIN snib.observacionescoordenadasconabio o ON cg.idobservacionescoordenadasconabio = o.idobservacionescoordenadasconabio
                    WHERE i.idejemplar = ?;"; 

            $stmt = $mysqli->prepare($sql);
            if ($stmt === false) {
                 throw new Exception("Error preparando la consulta: " . $mysqli->error);
            }

            $stmt->bind_param("s", $llaveejemplar);
            $stmt->execute();
            $stmt->bind_result(
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
                $observacionescoordenadasconabio,
                $urlorigen,
                $tipositio
            );


            if ($stmt->fetch()) {
                $result = array(
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
                    $observacionescoordenadasconabio,
                    $urlorigen,
                    $tipositio
                );
            } else {
                $result = array(); 
            }
            $stmt->close();
        } catch (Exception $e) {
            error_log("Error en obtenResumen: " . $e->getMessage());
            $result = array(); 
        }
        return $result;
    }

    function obtenProyecto($mysqli, $llaveejemplar): string
    {
        $urlProyecto = $titulo = $urlProyectoConabio = $urlOrigen = '';
        try {
            $sql = "SELECT titulo, urlproyectoconabio, urlorigen
                    FROM snib.ejemplar_curatorial
                    INNER JOIN snib.proyecto USING(llaveproyecto) WHERE llaveejemplar = ?";
            $stmt = $mysqli->prepare($sql);
            if ($stmt === false) {
                 throw new Exception("Error preparando la consulta de proyecto: " . $mysqli->error);
            }
            $stmt->bind_param("s", $llaveejemplar);
            $stmt->execute();
            $stmt->bind_result($titulo, $urlProyectoConabio, $urlOrigen);
            if ($stmt->fetch()) {
                $urlProyectoConabio = htmlspecialchars($urlProyectoConabio, ENT_QUOTES, 'UTF-8');
                $urlOrigen = htmlspecialchars($urlOrigen, ENT_QUOTES, 'UTF-8');
                $titulo = htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8');

                if ($urlProyectoConabio != '') {
                    $urlProyecto = "<a href='$urlProyectoConabio' target='_blank'>$titulo</a>";
                } else if ($urlOrigen != '') {
                    $urlProyecto = "<a href='$urlOrigen' target='_blank'>$titulo</a>";
                } else {
                    $urlProyecto = $titulo; 
                }
            } else {
                 $urlProyecto = ''; 
            }
            $stmt->close();
        } catch (Exception $e) {
            error_log("Error en obtenProyecto: " . $e->getMessage());
            $urlProyecto = ''; 
        }
        return $urlProyecto;
    }
    
}