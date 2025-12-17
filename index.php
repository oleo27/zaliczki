<?php
require_once __DIR__ . '/data/kwoty.php';
require_once __DIR__ . '/obliczenia.php';

$wynik = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $wynik = obliczZaliczke($_POST, $kwoty);
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Kalkulator zaliczki WFOŚiGW</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Kalkulator zaliczki WFOŚiGW</h1>

    <form method="post" action="">
        <!-- =================== DANE PODSTAWOWE =================== -->

        <!-- Numer dyspozycji -->
        <label>Numer dyspozycji wypłaty zaliczki:</label>
        <select name="nrDyspo" id="nrDyspo" required>
            <option value="">-- wybierz --</option>
            <option value="1">Pierwsza</option>
            <option value="2">Druga</option>
            <option value="3">Trzecia</option>
        </select>
        <br><br>

        <!-- Liczba złożonych wniosków -->
        <label>Liczba złożonych wniosków o płatność:</label>
        <select name="liczbaWop" id="liczbaWop" required>
            <option value="">-- wybierz --</option>
            <option value="1">Zero</option>
            <option value="2">Jeden</option>
            <option value="3">Dwa</option>
        </select>
        <br><br>

        <h2>Dane podstawowe</h2>

        <!-- Poziom dofinansowania -->
        <label>Poziom dofinansowania:</label>
        <select name="wyborDof" required>
            <option value="">-- wybierz --</option>
            <option value="1">Najwyższy</option>
            <option value="2">Podwyższony</option>
        </select>
        <br><br>

        <!-- Rodzaj przedsięwzięcia -->
        <label>Rodzaj przedsięwzięcia - zgodnie z wnioskiem o dofinansowanie:</label>
        <select name="wyborPrzedsiewziecia" required>
            <option value="">-- wybierz --</option>
            <option value="1">Nr 1 - wskaźnik zapotrzebowania na energię użytkową poniżej 80 kWh/(m²*rok)</option>
            <option value="2">Nr 2 - wskaźnik zapotrzebowania na energię użytkową między 80 a 140 kWh/(m²*rok) </option>
            <option value="3">Nr 3 - wskaźnik zapotrzebowania na energię użytkową powyżej 140 kWh/(m²*rok)</option>
        </select>
        <br><br>

        <label>Maksymalna kwota dotacji - zgodnie z wnioskiem o dofinansowanie:</label>
        <input type="number" step="0.01" name="maksDotacja" placeholder="0.00">
        <br><br>

        <label>Procent powierzchni budynku przeznaczony na działalność gospodarczą:</label>
        <input type="text" name="procentDzialalnosci" placeholder="0.00" onblur="this.value=parseFloat(this.value.replace(',', '.')).toFixed(2)">
        <label style="display:inline;">%</label>
        <br><br>

        
        <h2 id="headerKwoty" style="display:none;">Kwoty wnioskowane / wypłacone</h2>

        <div id="sekcjaZaliczki" style="display:none;">
            <label>Kwota zaliczek wnioskowanych/wypłaconych na przedsięwzięcia z Tab. 1 i 2 (tj. audyt, źródło ciepła, instalacja C.O. i C.W.U.):</label>
            <input type="text" name="zaliczkiPrzed12" placeholder="0.00" onblur="this.value=parseFloat(this.value.replace(',', '.')).toFixed(2)">
            <br><br>

            <label>Kwota zaliczek wnioskowanych/wypłaconych na przedsięwzięcia z Tab. 3 (tj. ocieplenie przegród, wymiana stolarki, wentylacja mechaniczna):</label>
            <input type="text" name="zaliczkiPrzed3" placeholder="0.00" onblur="this.value=parseFloat(this.value.replace(',', '.')).toFixed(2)">
            <br><br>
        </div>
        <div id="sekcjaWopy" style="display:none;">
            <label>Kwota dotacji wnioskowanych/wypłaconych na przedsięwzięcia z Tab. 1 i 2 (tj. audyt, źródło ciepła, instalacja C.O. i C.W.U.):</label>
            <input type="text" name="dotacjaPrzed12" placeholder="0.00" onblur="this.value=parseFloat(this.value.replace(',', '.')).toFixed(2)">
            <br><br>

            <label>Kwota dotacji wnioskowanych/wypłaconych na przedsięwzięcia z Tab. 3 (tj. ocieplenie przegród, wymiana stolarki, wentylacja mechaniczna):</label>
            <input type="text" name="dotacjaPrzed3" placeholder="0.00" onblur="this.value=parseFloat(this.value.replace(',', '.')).toFixed(2)">
            <br><br>
        </div>

        <!-- tabelka z kosztami -->
        <h2>Koszty kwalifikowane</h2>

        <table border="1" cellpadding="6" cellspacing="0">
            <thead>
                <tr>
                    <th>Wybór</th>
                    <th>Koszt</th>
                    <th>Liczba</th>
                    <th>Jednostka</th>
                    <th>Kwota wynagrodzenia netto (zł)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // ---- ŹRÓDŁA CIEPŁA -----
                echo '<tr><th colspan="5" style="text-align:left;">Źródło ciepła</th></tr>';
                echo "<tr>";
                echo "<td><input type='checkbox' name='nietermo_zc[wybor]' value='1'></td>";
                echo "<td colspan='2'>
                        <select name='zrodlo_ciepla' style='width:250px'>
                            <option value=''>-- wybierz źródło ciepła --</option>";
                foreach ($kwoty['najwyzszy_zc'] as $nazwa => $limit) {
                    echo "<option value='".$nazwa."'>$nazwa</option>";
                }
                echo "    </select>
                    </td>";
                echo "<td></td>";
                echo "<td><input type='number' step='0.01' name='zrodlo_kwota' style='width:120px'></td>";
                echo "</tr>";

                // ---- INNE (NIETERMO) ----
                echo '<tr><th colspan="5" style="text-align:left;">Inne koszty kwalifikowane</th></tr>';
                foreach ($kwoty['najwyzszy_pozostale'] as $nazwa => $limit) {
                    echo "<tr>";
                    echo "<td><input type='checkbox' name='nietermo[".$nazwa."][wybor]' value='1'></td>";
                    echo "<td>$nazwa</td>";
                    // pole "liczba" zablokowane
                    echo "<td><input type='number' step='1' name='nietermo[".$nazwa."][liczba]' value='' style='width:80px' disabled></td>";
                    echo "<td></td>";
                    echo "<td><input type='number' step='0.01' name='nietermo[".$nazwa."][kwota]' style='width:120px'></td>";
                    echo "</tr>";
                }

                // ---- TERMOMODERNIZACJA ----
                echo '<tr><th colspan="5" style="text-align:left;">Termomodernizacja</th></tr>';
                foreach ($kwoty['najwyzszy_termo'] as $nazwa => $limit) {
                    $wyswietlanaNazwa = $nazwa;
                    $jednostka = "m²";
                    $disabled = "";
                    if ($nazwa === "Wentylacja mechaniczna z odzyskiem ciepła") {
                        $wyswietlanaNazwa = "Wentylacja mechaniczna z odzyskiem ciepła - jednostka centralna";
                        $jednostka = "";              
                        $disabled = "disabled";         
                    }
                    if ($nazwa === "Wentylacja mechaniczna z odzyskiem ciepła - rekuperator ścienny") {
                        $jednostka = "szt.";
                    }
                    echo "<tr>";
                    echo "<td><input type='checkbox' name='termo[".$nazwa."][wybor]' value='1'></td>";
                    echo "<td>$wyswietlanaNazwa</td>";
                    echo "<td><input type='number' step='0.01' name='termo[".$nazwa."][liczba]' style='width:80px' $disabled></td>";
                    echo "<td>$jednostka</td>";
                    echo "<td><input type='number' step='0.01' name='termo[".$nazwa."][kwota]' style='width:120px'></td>";
                    echo "</tr>";
                }
                ?>

            </tbody>
        </table>

        <br>
        <button type="submit">Oblicz</button>
    </form>

    <?php if ($wynik): ?>
    <h2>Wyniki</h2>
    <?php if (isset($wynik['error'])): ?>
        <p style="color:red"><?= $wynik['error'] ?></p>
    <?php else: ?>
        <p>Dotacja – termomodernizacja: <?= number_format($wynik['dotacjaTermo'], 2, ',', ' ') ?> zł</p>
        <p>Dotacja – pozostałe: <?= number_format($wynik['dotacjaNietermo'], 2, ',', ' ') ?> zł</p>
        <p>Łączna wartość kwalifikowanych kosztów: <?= number_format($wynik['dotacja'], 2, ',', ' ') ?> zł</p>
        <p>Maksymalna zaliczka: <?= number_format($wynik['maksZaliczka'], 2, ',', ' ') ?> zł</p>
        <p>Wyliczona kwota zaliczki: <?= number_format($wynik['zaliczka'], 2, ',', ' ') ?> zł</p>
    <?php endif; ?>
<?php endif; ?>

<script src="js/main.js"></script>
</body>
</html>



