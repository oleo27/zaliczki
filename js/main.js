document.getElementById("nrDyspo").addEventListener("change", function () {
	const naglowek = document.getElementById("headerKwoty");
	const sekcja = document.getElementById("sekcjaZaliczki");
	if (this.value === "2" || this.value === "3") {
		naglowek.style.display = "block";
		sekcja.style.display = "block";
	} else {
		sekcja.style.display = "none";
		sekcja.style.display = "none";
	}
});

document.getElementById("liczbaWop").addEventListener("change", function () {
	const naglowek = document.getElementById("headerKwoty");
	const sekcja = document.getElementById("sekcjaWopy");
	if (this.value === "2" || this.value === "3") {
		naglowek.style.display = "block";
		sekcja.style.display = "block";
	} else {
		sekcja.style.display = "none";
		sekcja.style.display = "none";
	}
});

document.addEventListener("DOMContentLoaded", () => {
	function restrictDecimals(input, decimals = 2) {
		input.addEventListener("input", function () {
			let value = this.value;
			value = value.replace(/\./g, ",");

			const parts = value.split(",");

			if (parts[1]) {
				parts[1] = parts[1].substring(0, decimals);
				value = parts[0] + "," + parts[1];
			}
			value = value.replace(/[^0-9,]/g, "");

			this.value = value;
		});
	}
	document
		.querySelectorAll(".kwota, .metry")
		.forEach((input) => restrictDecimals(input, 2));
});

function scrollToWynik() {
	const wynikElement = document.getElementById("wyniki");
	if (wynikElement) {
		wynikElement.scrollIntoView({ behavior: "smooth" });
	}
}

// Wywołanie funkcji tylko jeśli istnieje wynik
if (document.getElementById("wyniki")) {
	scrollToWynik();
}

document.addEventListener("DOMContentLoaded", function () {
	document.querySelectorAll("tbody tr").forEach(function (row) {
		const checkbox = row.querySelector("input[type='checkbox']");
		if (!checkbox) return;

		const fields = row.querySelectorAll("input[type='text'], select");

		function syncState() {
			fields.forEach(function (f) {
				if (checkbox.checked) {
					f.removeAttribute("disabled");
				} else {
					f.setAttribute("disabled", "disabled");
					// czyść wartości
					if (f.tagName === "INPUT") f.value = "";
					if (f.tagName === "SELECT") f.selectedIndex = 0;
				}
			});
		}

		syncState();

		checkbox.addEventListener("change", syncState);
	});
});
