<?php
function obliczZaliczke(array $post, array $kwoty): array {
    $wynik = [];

    // zczytanie danych z formularza
    $wyborDof = $post['wyborDof'];
    $wyborPrzedsiewziecia = $post['wyborPrzedsiewziecia'];
    $maksDotacja = floatval($post['maksDotacja']);

    // maksymalna zaliczka
    $maksZaliczka = 0.35 * $maksDotacja;
    $maksZaliczka -= floatval($post['zaliczkiPrzed12']) ?? 0;
    $maksZaliczka -= floatval($post['zaliczkiPrzed3']) ?? 0;
    $wynik['maksZaliczka'] = $maksZaliczka;

    // dobór słowników i limitów
    if ($wyborDof === '1') {
        $slownik = [
            'nietermo_zc' => $kwoty['najwyzszy_zc'],
            'nietermo_pozostale' => $kwoty['najwyzszy_pozostale'],
            'termo' => $kwoty['najwyzszy_termo']
        ];
        $maksTermoLimit = $kwoty['maksTermoNajw'];
    } elseif ($wyborDof === '2') {
        $slownik = [
            'nietermo_zc' => $kwoty['podwyzszony_zc'],
            'nietermo_pozostale' => $kwoty['podwyzszony_pozostale'],
            'termo' => $kwoty['podwyzszony_termo']
        ];
        $maksTermoLimit = match ($wyborPrzedsiewziecia) {
            '2' => $kwoty['maksTermo2podw'],
            '3' => $kwoty['maksTermo3podw'],
            default => 0
        };
    }
    
    // obliczenie limitu termo
    $maksTermoLimit -= floatval($post['zaliczkiPrzed3']) ?? 0;
    $maksTermoLimit -= floatval($post['dotacjaPrzed3']) ??0;

    // zmienne startowe
    $dotacjaTermo = 0;
    $dotacjaNietermo = 0;

    // Obliczenie dotacji na źródło ciepła
    if (!empty($post['nietermo_zc']['wybor'])){

        $nazwa = $post['zrodlo_ciepla'];
        $kwota = floatval($post['zrodlo_kwota']);

        $limit = $slownik['nietermo_zc'][$nazwa];
        $kwotaDotacjiZc = ($wyborDof === '2')
            ? min($kwota * 0.7, $limit)
            : min($kwota, $limit);

        $dotacjaNietermo += $kwotaDotacjiZc;
    }

    // Obliczenie innych kosztów nietermo
    if (!empty($post['nietermo'])) {
        foreach ($slownik['nietermo_pozostale'] as $nazwa => $limit) {
            $wybor = $post['nietermo'][$nazwa]['wybor'] ?? 0;
            $kwota  = floatval($post['nietermo'][$nazwa]['kwota'] ?? 0);

            if (!$wybor || $kwota <= 0) continue;

            $kwotaDotacjiPozostale = ($wyborDof === '2')
                ? min($kwota * 0.7, $limit)
                : min($kwota, $limit);

            $dotacjaNietermo += $kwotaDotacjiPozostale;
        }
    }

    // Obliczenia kosztów termo
    if (!empty($post['termo'])) {
        foreach ($slownik['termo'] as $nazwa => $limit) {
            $wybor = $post['termo'][$nazwa]['wybor'] ?? 0;
            $liczba = floatval($post['termo'][$nazwa]['liczba'] ?? 0);
            $kwota  = floatval($post['termo'][$nazwa]['kwota'] ?? 0);

            if (!$wybor || $kwota <= 0) continue;

            $maxLimit = $limit * $liczba;
            $kwotaDotacjiTermo = ($wyborDof === '2')
                ? min($kwota * 0.7, $maxLimit)
                : min($kwota, $maxLimit);

            $dotacjaTermo += $kwotaDotacjiTermo;
        }
    }

    // Obliczenia dla rekuperacji
    $rekuperatorScienny   = 'Wentylacja mechaniczna z odzyskiem ciepła - rekuperator ścienny';
    $jednostkaCentralna   = 'Wentylacja mechaniczna z odzyskiem ciepła';

    $wyborS   = $post['termo'][$rekuperatorScienny]['wybor']  ?? 0;
    $liczbaS  = floatval($post['termo'][$rekuperatorScienny]['liczba'] ?? 0);
    $kwotaS   = floatval($post['termo'][$rekuperatorScienny]['kwota']  ?? 0);

    $wyborJC  = $post['termo'][$jednostkaCentralna]['wybor']  ?? 0;
    $kwotaJC  = floatval($post['termo'][$jednostkaCentralna]['kwota']  ?? 0);

    $kwotaReku = 0;

    if ($wyborS && $kwotaS > 0) {
        $limitS = $slownik['termo'][$rekuperatorScienny] * $liczbaS;
        $kwotaReku = min($kwotaS, $limitS); 
    }

    $kwotaCentralna = 0;

    if ($wyborJC && $kwotaJC > 0) {
        $kwotaCentralna = $kwotaJC;  // jednostka centralna bez osobnego limitu
    }

    $kwotaSuma = $kwotaReku + $kwotaCentralna;

    $maksLimitWentylacja = $slownik['termo'][$jednostkaCentralna];

    $kwotaDotacjiWentylacja = ($wyborDof === '2')
    ? min($kwotaSuma * 0.7, $maksLimitWentylacja * 0.7)  
    : min($kwotaSuma, $maksLimitWentylacja);


    // Dodanie rekuperacji do dotacji termo
    $dotacjaTermo += $kwotaDotacjiWentylacja;

    if ($dotacjaTermo > $maksTermoLimit){
        $dotacjaTermo = $maksTermoLimit;
    }

    $dotacja = $dotacjaTermo + $dotacjaNietermo;

    $procentDzialalnosc = floatval($_POST['procentDzialalnosci'])/100;
    $dotacja = $dotacja * (1-$procentDzialalnosc);

    $zaliczka = min($dotacja*0.35, $maksZaliczka);
    $wynik['dotacja'] = $dotacja;
    $wynik['zaliczka'] = $zaliczka;
    

    return $wynik;
}
