//inicializaciones globales
$(function () {
	$('[data-toggle="tooltip"]').tooltip();
})

Pace.on("start", function(){
	$div=$('<div>').css ({
		'position': 'fixed',
		'top':'0',
		'left':'0',
		'width': '100%',
		'height': '100%',
		'background-color': 'rgba(255,255,255,0.73)',
		'z-index':'1047',
	}).addClass('pace-overlay');
	$div.prependTo('body');
});

Pace.on("done", function(){
	$(".pace-overlay").remove();
});

//Objetos de configuración
aagmGlobalConfig={
	datetimerangeLocale: {
		"format": "YYYY-MM-DD",
		"separator": " - ",
		"applyLabel": "Acepar",
		"cancelLabel": "Cancelar",
		"fromLabel": "Desde",
		"toLabel": "Hasta",
		"customRangeLabel": "Personalizado",
		"weekLabel": "S",
		"daysOfWeek": [
			"Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"
		],
		"monthNames": [
			"Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ,
		],
		"firstDay": 1
	}
};





//funciones varias
function arrayOfObjectsToHtmlTable(data,roundNumbers=-1,stripTags=false) {
	var result='<table><thead>';
	for(var i = 0; i < data.length; i++) {
		if (i==0) {
			result+="<tr>";
			object=data[i];
			for (const key in object) {
				if (object.hasOwnProperty(key)) {
					result+="<th>"+key+"</th>";
				}
			}
			result+="</tr>";
			result+="</thead>";
			result+="<tbody>";
		}
		result+="<tr>";
		object=data[i];
		for (const key in object) {
			if (object.hasOwnProperty(key)) {
				if (stripTags) {
					var html = object[key];
					var div = document.createElement("div");
					div.innerHTML = html;
					var text = div.textContent || div.innerText || "";
					var tdContent=text;
				} else {
					var tdContent=object[key];
				}
				if (roundNumbers>-1) {
					var roundTry=parseFloat(object[key]);
					if (!isNaN(roundTry)) {
						//tdContent=Math.round(roundTry*1000)/1000;
						//tdContent=+roundTry.toFixed(2);
						tdContent=roundTry.toLocaleString('de-DE', { maximumFractionDigits: roundNumbers});
					}
				}
				result+="<td>"+tdContent+"</td>";
			}
		}
		result+="</tr>";
	}
	result+='</tbody></table>';
	return result;
}

//funciones varias
function arrayOfObjectsToHtmlTableCatalogo(data, roundNumbers = -1, stripTags = false) {
	var result = '<table><thead>';
	for (var i = 0; i < data.length; i++) {

		if (i == 0) {
			result += "<tr>";
			object = data[i];
			for (const key in object) {
				if (object.hasOwnProperty(key)) {
					result += "<th>" + key + "</th>";
				}
			}
			result += "</tr>";
			result += "</thead>";
			result += "<tbody>";
		}
		result += "<tr>";
		object = data[i];
		var cont = 0;
		for (const key in object) {
			if (object.hasOwnProperty(key)) {
				if (stripTags) {
					var html = object[key];
					var div = document.createElement("div");
					div.innerHTML = html;
					var text = div.textContent || div.innerText || "";
					var tdContent = text;
				} else {
					var tdContent = object[key];
					//console.log(tdContent);
				}
				if (roundNumbers > -1) {
					if (cont == 3 || cont == 4) {
						var roundTry = parseFloat(object[key]);
						if (!isNaN(roundTry)) {
							//tdContent=Math.round(roundTry*1000)/1000;
							//tdContent=+roundTry.toFixed(2);
							tdContent = roundTry.toLocaleString('es-ES', {maximumFractionDigits: roundNumbers});
						}
					}
				}
				if(cont == 1) {
					result += "<td style='min-width:175px; text-align: center;'>" + tdContent + "</td>";
				}else {
					result += "<td style='padding-left: 15px;'>" + tdContent + "</td>";
				}
				cont = cont + 1;
			}
		}
		result += "</tr>";
	}
	result += '</tbody></table>';
	return result;
}


function parseNumber(value, locale = navigator.language) {
	const example = Intl.NumberFormat(locale).format('1.1');
	const cleanPattern = new RegExp(`[^-+0-9${ example.charAt( 1 ) }]`, 'g');
	const cleaned = value.replace(cleanPattern, '');
	const normalized = cleaned.replace(example.charAt(1), '.');
	return parseFloat(normalized);
}
function exportTableToExcel(tableNode, sheetName='', filename='') {
	sheetName = sheetName || 'Hoja 1';
	filename = filename || 'InformePedidos.xlsx';
	//var wb = XLSX.utils.table_to_book(document.getElementById('mytable'), {sheet:sheetName});

	var rows = $("tr",tableNode).map(function() {
		return [$("th,td",this).map(function() {
			var text=this.textContent || this.innerText || "";
			return parseNumber(text) || text;
		}).get()];
	}).get();
	console.log(rows);

	var ws = XLSX.utils.aoa_to_sheet(rows);
	var wb = XLSX.utils.book_new();
	XLSX.utils.book_append_sheet(wb, ws, 'Informe pedidos');
	//var wb = XLSX.utils.table_to_book(tableNode, {sheet:sheetName,raw:true});

	/*var ws = wb.Sheets[sheetName]; //get the current sheet
	console.log(ws["C2"].v); //  default v value '4.56'

	ws["C2"].z = "0.00"; //  format the cell

	delete ws["C2"].w; // delete old formatted text if it exists
	XLSX.utils.format_cell(ws["C2"]); // refresh cell

	console.log(ws["C2"].w); // new formatted cell '$4.56'
	*/

	var wbout = XLSX.write(wb, {bookType:'xlsx', bookSST:true, type: 'binary'});
	function s2ab(s) {
		var buf = new ArrayBuffer(s.length);
		var view = new Uint8Array(buf);
		for (var i=0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
		return buf;
	}
	//$("#button-a").click(function(){
	filesaver.saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), filename);
	//});
}
