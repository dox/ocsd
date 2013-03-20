(function () {
	var input = document.getElementById("images"),
	formdata = false;
	
	function showUploadedItem (source) {
		var list = document.getElementById("image-list"),
		img  = document.createElement("img");
		
		img.src = source;
		list.appendChild(img);
		//list.appendChild(li);
		//alert(source);
	}
	
	if (window.FormData) {
  		formdata = new FormData();
  		document.getElementById("btn").style.display = "none";
	}
	
 	input.addEventListener("change", function (evt) {
 		document.getElementById("response").innerHTML = "Uploading . . ."
 		var i = 0, len = this.files.length, img, reader, file;
	
		for ( ; i < len; i++ ) {
			file = this.files[i];
	
			if (!!file.type.match(/image.*/)) {
				if ( window.FileReader ) {
					reader = new FileReader();
					reader.onloadend = function (e) { 
						showUploadedItem(e.target.result, file.fileName);
					};
					reader.readAsDataURL(file);
				}
				if (formdata) {
					formdata.append("images[]", file);
				}
			}	
		}
		
		var studentkey = $("input#studentkey").val();
		if (formdata) {
			$.ajax({
				url: "modules/students/actions/photoUpload.php?studentkey=" + studentkey,
				type: "POST",
				data: formdata,
				processData: false,
				contentType: false,
				success: function (res) {
					document.getElementById("response").innerHTML = res; 
				}
			});
		}
	}, false);
}());
