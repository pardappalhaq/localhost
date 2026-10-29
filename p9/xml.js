$(document).ready(function () {
	var mhs;
	var j = 0;
	
	$.ajax({
		url: 'data.xml',
		dataType: 'xml',
		success: function (res) {
			var i = 0;
			mhs = $(res).find('mhs');
			
			mhs.each(function () {
				var m = getMhs(i);
				
				$('tbody').append('<tr><td>' + (++i) + '</td><td>' + m.nim + '</td><td>' + m.nama + '</td></tr>');
			});
			
			if (!mhs.length) { //mhs.length == 0
				$('tbody').append('<tr><td colspan="3" class="text-center">Tidak ada data</td></tr>');
			}
			
			if (mhs.length < 2) {
				$('#next').prop('disabled', true);
			}
		}
	});
	
	function getMhs(i) {
		var nim = $(mhs[i]).children('nim').text();
		var nama = $(mhs[i]).children('nama').text();
		var foto = $(mhs[i]).children('foto').text();
		
		if (!i) { // i == 0
			$('#nim').val(nim);
			$('#nama').val(nama);
			$('#foto').prop('src', 'images/' + foto);
		}
		
		return {'nim': nim, 'nama': nama, 'foto': foto};
	}
	
	$('#next').click(function () {
		if (j < mhs.length - 1) {
			var m = getMhs(++j);
			
			$('#nim').val(m.nim);
			$('#nama').val(m.nama);
			$('#foto').prop('src', 'images/' + m.foto);
			
			$('#prev').prop('disabled', false);
		}
		
		if (j == mhs.length - 1) {
			$(this).prop('disabled', true);
		}
	});
	
	$('#prev').click(function () {
		if (j >= 0) {
			var m = getMhs(--j);
			
			$('#nim').val(m.nim);
			$('#nama').val(m.nama);
			$('#foto').prop('src', 'images/' + m.foto);
			
			$('#next').prop('disabled', false);
		}
		
		if (!j) { // j == 0
			$(this).prop('disabled', true);
		}
	});
});