//var galleryImages;

$(document).on( 'pageinit',function(event){
	getCamera();
	
});


function takePicture(){
	
	$.mobile.loading( 'show', {
		text: 'Taking Image....',
		textVisible: true,
		theme: 'a'
	});
         
	
	$.ajax({
		url: "service.php?action=takePicture",
		dataType : "json",
		success: function(data){
			$.mobile.loading( 'hide');
		},
	});
}

function setOwner(){
	
	$.mobile.loading( 'show', {
		text: 'Setting Owner....',
		textVisible: true,
		theme: 'a'
	});
    
    var formData = {
            'ownerName'              : $('input[name=ownerName]').val(),
            'authorName'             : $('input[name=authorName]').val()
        };     
	
	$.ajax({
		url: "service.php?action=setOwner",
		data     : formData,
		dataType : "json",
		success: function(data){
			$.mobile.loading( 'hide');
		},
	});
}


$(document).on( "pageshow","#gallery", function( event ) {
	$.ajax({
		url: "service.php?action=getImages",
		dataType : "json",
		success: function(data){
			updateGalleryGrid(data);
		},
	});
})


function updateGalleryGrid(data){
	//$("#galleryGrid").html("");
	
	var galleryHTML = "";
	
	for(var i = 0; i < data.length; i++){
	
	
	
		var uiClass = "a";
		
		if (i % 2 == 1){
			uiClass = "b";
		} 
	
		var image = data[i];

		var id = image.name.replace(/[-\.]/g,'');

		if ($('#' + id).length	> 0){
			$('#' + id).removeClass("ui-block-a");
			$('#' + id).removeClass("ui-block-b");
			$('#' + id).addClass("ui-block-" + uiClass);					
		}else{
			var galleryTemplate = $("#galleryTemplate").text();
			galleryTemplate = galleryTemplate.replace(/@imageThumb/g, image.thumbPath);
			galleryTemplate = galleryTemplate.replace(/@char/g, uiClass);
			galleryTemplate = galleryTemplate.replace(/@sourceURL/g, image.sourcePath);
			galleryTemplate = galleryTemplate.replace(/@imageName/g, image.name);	
			galleryTemplate = galleryTemplate.replace(/@id/g, id);	
			$("#galleryGrid").append(galleryTemplate);

		}
	}
}

$(document).on( 'pageinit',function(event){
	getCamera();
});

function deleteFile(file){

	var id = file.replace(/[-\.]/g,'');
	$('#' + id).remove();

	$.ajax({
		url: "service.php?action=deleteFile&file=" + file,
		dataType : "json",
		success: function(data){		
			$.ajax({
				url: "service.php?action=getImages",
				dataType : "json",
				success: function(data){
					updateGalleryGrid(data);
				},
			});					
		},
	});
}

function getCamera(){
	$.ajax({
		url: "service.php?action=getCamera",
		dataType : "json",
		success: function(data){
			$("#cameraName").html(data.camera);
			getOwner();
		},
	});
}

function getOwner(){
	$.ajax({
		url: "service.php?action=getOwner",
		dataType : "json",
		success: function(data){
			console.log(data);
			$("#ownerName").val(data.owner);
			
		},
	});
}

function getArtist(){
	$.ajax({
		url: "service.php?action=getArtist",
		dataType : "json",
		success: function(data){
			console.log(data);
			$("#artistName").val(data.artist);
			
		},
	});
}