let i = 0;
while (i < 16) {
	let k = 0;
	while (k < 16) {
		let id = i.toString()+"_"+k.toString();
		document.getElementById(id).onclick = function() {
			console.log("click capté");
			document.getElementById(id).children[0].submit();
		}
		k += 1;
	}
	i += 1;
}