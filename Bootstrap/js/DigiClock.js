var clock = new Vue({
    el: '#clock',
    data: {
        time: '',
        date: ''
    }
});

var week = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
var timerID = setInterval(updateTime, 1000);
updateTime();
function updateTime() {
	var cd = new Date();
	
	  var hours = cd.getHours()
	  var minutes = cd.getMinutes();
	  var seconds = cd.getSeconds();
	  var ampm = hours >= 12 ? 'PM' : 'AM';
	  
	  hours = hours % 12;
	  hours = hours ? hours : 12; // the hour '0' should be '12'
	  minutes = minutes < 10 ? '0'+minutes : minutes;
	
    
    clock.time = zeroPadding(hours, 2) + ':' + zeroPadding(minutes, 2) + ':' + zeroPadding(seconds, 2) + ' ' + ampm;
    clock.date = zeroPadding(cd.getMonth()+1, 2) + '/' +   zeroPadding(cd.getDate(), 2) + '/' +  zeroPadding(cd.getFullYear(), 4) + ' ' + week[cd.getDay()];
};

function zeroPadding(num, digit) {
    var zero = '';
    for(var i = 0; i < digit; i++) {
        zero += '0';
    }
    return (zero + num).slice(-digit);
}