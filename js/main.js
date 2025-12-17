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
	document.querySelectorAll('input[step="0.01"]').forEach((i) => {
		i.addEventListener(
			"input",
			() =>
				(i.value = i.value
					.replace(",", ".")
					.replace(/^(\d+)(\.\d{0,2})?.*$/, "$1$2"))
		);

		i.addEventListener("blur", () => {
			if (i.value) i.value = parseFloat(i.value.replace(",", ".")).toFixed(2);
		});

		i.form?.addEventListener("submit", () => {
			if (i.value) i.value = i.value.replace(",", ".");
		});
	});
});
