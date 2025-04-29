<?php
class enciclovida
{
    function ligaComentarios($mysqli, $llaveejemplar): string
    {
        //arreglos de los diferentes catalogos [01-Echinodermata, 02-Crustaceos, 03-Hongos, 04-Invertebrados, 05-Plantas, 06-Algas, 07-Vertebrados, 08-Diptera, 09-Arthropoda]
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
                WHERE llaveejemplar = '{$llaveejemplar}'";
        $result = $mysqli->query($sql);
        $row = $result->fetch_array(MYSQLI_NUM);
        $catalogo = "";
        $idcat = 0;
        $cod = 0;
        $catalogo = preg_replace('/[0-9]+/', '', $row[0]);
        $idcat = intval(preg_replace('/[^0-9]+/', '', $row[0]), 10);


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
        $result->free();
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
            $datumoriginal = $tipovegetacion = $fuentegeorreferenciacion = $tipovegetacionmapa = '';
        $latitudFormateada = $longitudFormateada = $incertidumbreXY = '';
        try {
            $sql = "SELECT
                        especievalida, autorvalido, TRIM(BOTH ',' FROM REGEXP_REPLACE(nombrecomun, '[^,]*\\\\([^)]*\\\\),? ?', '')) as nombrecomun,
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
                                IF (((g.latitudgrados IS NOT NULL AND g.latitudgrados<>999) OR (g.latitudminutos IS NOT NULL AND g.latitudminutos<>99) OR (g.latitudsegundos IS NOT NULL AND g.latitudsegundos<>99)),
                                    CONCAT(
                                      IF(g.latitudgrados IS NOT NULL AND g.latitudgrados<>999,CONCAT(g.latitudgrados,'°') ,''),
                                      IF(g.latitudminutos IS NOT NULL AND g.latitudminutos<>99,CONCAT(g.latitudminutos,\"'\",''),
                                        IF(g.latitudminutos= 99 AND g.latitudgrados IS NOT NULL AND g.latitudgrados <> 999 AND g.latitudsegundos IS NOT NULL AND g.latitudsegundos <> 99,CONCAT('0', \"'\",''),'')),
                                      IF(g.latitudsegundos IS NOT NULL AND g.latitudsegundos<>99,CONCAT(g.latitudsegundos,'\"'),''),' ',g.nortesur),
                                NULL)
                            WHEN c.observacionescoordenadasconabio LIKE 'Conversión de UTM%' THEN
                                CAST(g.utm_latitud AS CHAR)
                            WHEN c.observacionescoordenadasconabio LIKE 'Grados decimales%' THEN
                                SUBSTRING_INDEX(REPLACE(REPLACE(ST_AsText(g.coordenadaoriginal), 'POINT(', ''), ')', ''), ' ', -1)
                            WHEN c.observacionescoordenadasconabio LIKE 'Georreferenciado%' THEN
                                SUBSTRING_INDEX(REPLACE(REPLACE(ST_AsText(g.coordenadaoriginal), 'POINT(', ''), ')', ''), ' ', -1)
                            WHEN c.observacionescoordenadasconabio LIKE 'Coordenada Georrectificada%' THEN
                                CASE
                                    WHEN (g.latitudgrados IS NOT NULL) THEN
                                        IF (((g.latitudgrados IS NOT NULL AND g.latitudgrados<>999) OR (g.latitudminutos IS NOT NULL AND g.latitudminutos<>99) OR (g.latitudsegundos IS NOT NULL AND g.latitudsegundos<>99)),
                                            CONCAT(
                                              IF(g.latitudgrados IS NOT NULL AND g.latitudgrados<>999,CONCAT(g.latitudgrados,'°') ,''),
                                              IF(g.latitudminutos IS NOT NULL AND g.latitudminutos<>99,CONCAT(g.latitudminutos,\"'\",''),
                                                IF(g.latitudminutos= 99 AND g.latitudgrados IS NOT NULL AND g.latitudgrados <> 999 AND g.latitudsegundos IS NOT NULL AND g.latitudsegundos <> 99,CONCAT('0', \"'\",''),'')),
                                              IF(g.latitudsegundos IS NOT NULL AND g.latitudsegundos<>99,CONCAT(g.latitudsegundos,'\"'),''),' ',g.nortesur),
                                        NULL)
                                    WHEN (((g.latitudgrados IS NULL) OR (g.latitudgrados = 99)) AND (g.coordenadaoriginal IS NULL)) THEN
                                        CAST(g.utm_latitud AS CHAR)
                                    WHEN (((g.latitudgrados IS NULL) OR (g.latitudgrados = 99)) AND (g.utm_latitud IS NULL)) THEN
                                         SUBSTRING_INDEX(REPLACE(REPLACE(ST_AsText(g.coordenadaoriginal), 'POINT(', ''), ')', ''), ' ', -1)
                                    ELSE NULL
                                END
                            ELSE NULL
                        END AS latitud_formateada,
                        CASE
                            WHEN c.observacionescoordenadasconabio LIKE 'Conversión de sexagesimal%' THEN
                                IF (((g.longitudgrados IS NOT NULL AND g.longitudgrados<>999) OR (g.longitudminutos IS NOT NULL AND g.longitudminutos<>99) OR (g.longitudsegundos IS NOT NULL AND g.longitudsegundos<>99)),
                                    CONCAT(
                                      IF(g.longitudgrados IS NOT NULL AND g.longitudgrados<>999,CONCAT(g.longitudgrados,'°') ,''),
                                      IF(g.longitudminutos IS NOT NULL AND g.longitudminutos<>99,CONCAT(g.longitudminutos,\"'\",''),
                                        IF(g.longitudminutos= 99 AND g.longitudgrados IS NOT NULL AND g.longitudgrados <> 999 AND g.longitudsegundos IS NOT NULL AND g.longitudsegundos <> 99,CONCAT('0', \"'\",''),'')),
                                      IF(g.longitudsegundos IS NOT NULL AND g.longitudsegundos<>99,CONCAT(g.longitudsegundos,'\"'),''),' ',g.esteoeste),
                                NULL)
                            WHEN c.observacionescoordenadasconabio LIKE 'Conversión de UTM%' THEN
                                CAST(g.utm_longitud AS CHAR)
                            WHEN c.observacionescoordenadasconabio LIKE 'Grados decimales%' THEN
                                 SUBSTRING_INDEX(SUBSTRING_INDEX(REPLACE(REPLACE(ST_AsText(g.coordenadaoriginal), 'POINT(', ''), ')', ''), ' ', 1), ' ', -1)
                            WHEN c.observacionescoordenadasconabio LIKE 'Georreferenciado%' THEN
                                 SUBSTRING_INDEX(SUBSTRING_INDEX(REPLACE(REPLACE(ST_AsText(g.coordenadaoriginal), 'POINT(', ''), ')', ''), ' ', 1), ' ', -1)
                            WHEN c.observacionescoordenadasconabio LIKE 'Coordenada Georrectificada%' THEN
                                CASE
                                    WHEN (g.latitudgrados IS NOT NULL) THEN
                                        IF (((g.longitudgrados IS NOT NULL AND g.longitudgrados<>999) OR (g.longitudminutos IS NOT NULL AND g.longitudminutos<>99) OR (g.longitudsegundos IS NOT NULL AND g.longitudsegundos<>99)),
                                            CONCAT(
                                              IF(g.longitudgrados IS NOT NULL AND g.longitudgrados<>999,CONCAT(g.longitudgrados,'°') ,''),
                                              IF(g.longitudminutos IS NOT NULL AND g.longitudminutos<>99,CONCAT(g.longitudminutos,\"'\",''),
                                                IF(g.longitudminutos= 99 AND g.longitudgrados IS NOT NULL AND g.longitudgrados <> 999 AND g.longitudsegundos IS NOT NULL AND g.longitudsegundos <> 99,CONCAT('0', \"'\",''),'')),
                                              IF(g.longitudsegundos IS NOT NULL AND g.longitudsegundos<>99,CONCAT(g.longitudsegundos,'\"'),''),' ',g.esteoeste),
                                        NULL)
                                    WHEN (((g.latitudgrados IS NULL) OR (g.latitudgrados = 99)) AND (g.coordenadaoriginal IS NULL)) THEN
                                        CAST(g.utm_longitud AS CHAR)
                                    WHEN (((g.latitudgrados IS NULL) OR (g.latitudgrados = 99)) AND (g.utm_latitud IS NULL)) THEN
                                         SUBSTRING_INDEX(SUBSTRING_INDEX(REPLACE(REPLACE(ST_AsText(g.coordenadaoriginal), 'POINT(', ''), ')', ''), ' ', 1), ' ', -1)
                                    ELSE NULL
                                END
                            ELSE NULL
                        END AS longitud_formateada,
                           
                        v.tipovegetacionmapa, i.incertidumbreXY
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
                    WHERE i.idejemplar = ?;";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("s", $llaveejemplar);
            $stmt->execute();
            $stmt->bind_result(
                // Variables originales
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
                $latitudFormateada,
                $longitudFormateada,
                $tipovegetacionmapa,
                $incertidumbreXY
            );

            if ($stmt->fetch()) {
                $result = array(
                    // Variables originales
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
                    $latitudFormateada,
                    $longitudFormateada,
                    $tipovegetacionmapa,
                    $incertidumbreXY
                );
            } else {
                $result = array();
            }
            $stmt->close();
        } catch (Exception $e) {
            $scientificName = '';
            echo "Se produjo un error: " . $e->getMessage();
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
            $stmt->bind_param("s", $llaveejemplar);
            $stmt->execute();
            $stmt->bind_result($titulo, $urlProyectoConabio, $urlOrigen);
            if ($stmt->fetch()) {
                if ($urlProyectoConabio != '') {
                    $urlProyecto = "<a href='$urlProyectoConabio' target='_blank'>$titulo</a>";
                } else if ($urlOrigen != '') {
                    $urlProyecto = "<a href='$urlOrigen' target='_blank'>$titulo</a>";
                } else $urlProyecto = $titulo;
            }
            // cerramos el statement y la conexión
            $stmt->close();
        } catch (Exception $e) {
            // Manejo de excepciones
            $urlProyecto = '';
            echo "Se produjo un error: " . $e->getMessage();
        }
        return $urlProyecto;
    }
}
