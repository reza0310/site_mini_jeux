let hauteur = 0;
let hia = 0;
let barre = document.getElementById("joueur");
let balle = document.getElementById("balle");
let ia = document.getElementById("ia");
document.addEventListener('keydown', logKey);
barre.style.top = hauteur.toString() + "em";
let bx = 15;
let by = 30;
let bsx = 1;
let bsy = 1;
let decaleur = 0;
let echanges = 0;
let termine = true;

function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

async function b1() {
	while (termine) {
		balle.style.top = by.toString() + "em";
		balle.style.left = bx.toString() + "em";
		
		bx += bsx;
		if (decaleur == 5) {
			by += bsy;
			decaleur = 0;
			echanges += 1;
		} else {
			decaleur += 1;
		}
		
		if (bx >= 90) {
			bsx = -1;
			if (bx > 88 && (hia > by || by > hia+10)) {
				termine = false;
				alert("Vous avez gagné!");
			}
		} else if (bx <= 5) {
			bsx = 1;
			if (bx < 7 && (hauteur > by || by > hauteur+10)) {
				termine = false;
				alert("Vous avez perdu!");
			}
		}
		
		if (by >= 44) {
			bsy = -1;
		} else if (by <= 3) {
			bsy = 1;
		}
		await sleep(20);
	}
}

async function b2() {
	while (termine) {
		ia.style.top = hia.toString() + "em";
		
		if (by > hia && hia < 36) {
			hia += 1;
		} else if (hia > 0) {
			hia -= 1;
		}
		await sleep(40+echanges);
	}
}

function logKey(e) {
	if (termine) {
		if (e.code == "ArrowUp" || e.code == "KeyZ" || e.code == "KeyW" || e.code == "Numpad8") {
			hauteur -= 1;
			if (hauteur < 0) {
				hauteur = 0;
			}
		} else if (e.code == "ArrowDown" || e.code == "KeyS" || e.code == "Numpad2") {
			hauteur += 1;
			if (hauteur > 36) {
				hauteur = 36;
			}
		}
		console.log(hauteur);
		barre.style.top = hauteur.toString() + "em";
	}
}

b1();
b2();