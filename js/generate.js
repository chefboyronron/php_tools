/**********************************************************************************************
 *	Ccstools Ver. 1.0.0 for developer  BY: ccs0rs00
 * Ccstools also available using php
 *
 * For security database name, table name and auto increment  column are set here
 * change the path where ajax/generated.php located
**********************************************************************************************/
Generate = {
	generateCode : function(elem){
		$.ajax({
			url : document.location.origin+"/snippets_ron/ccstools/ajax/generate.php",
			type : "POST",
			cache : false,
			data : {
				mode : "generateCode",
				base_format : "RGPS-",
				zero_format : "0000000000000"
			},
			beforeSend : function(){
				
			},
			success : function(result){
				$("."+elem).html(result);
			}
		}).error(function(x,y,z){
			console.log("error",x,y,z);
		});
	},
	generateUID : function(elem){
		$.ajax({
			url : document.location.origin+"/snippets_ron/ccstools/ajax/generate.php",
			type : "POST",
			cache : false,
			data : {
				mode : "generateUID"
			},
			beforeSend : function(){
				
			},
			success : function(result){
				$("."+elem).html(result);
			}
		}).error(function(x,y,z){
			console.log("error",x,y,z);
		});
	}
}