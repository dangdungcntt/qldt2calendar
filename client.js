var sct = document.createElement('script')
sct.src = 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js'
document.querySelector('body').append(sct);
sct.onload = function (e) {
    var list = Array.from(document.querySelectorAll('#gridRegistered tbody tr:not(:first-child):not(:last-child)'))

    var data = [];
    list.forEach(el => {
        var lopHocPhan = $(el).find('[id^=gridRegistered_lblCourseClass]').text().trim()
        var thoiGian = $(el).find('[id^=gridRegistered_lblLongTime]').text().trim()
        var diaDiem = $(el).find('[id^=gridRegistered_lblLocation]').text().trim()

        if (thoiGian.indexOf('TH') > -1) {
            return;
        }
        var arrThoiGian = [];
        var lastRange = '';
        var lastIs = 'range';
        thoiGian.split(/: \(\d\)|:|\(\w+\)/).filter(e => e.trim().length > 0).map(e => e.trim()).forEach(e => {
            if (e.indexOf('Từ') > -1) {
                lastRange = e;
                lastIs = 'range';
            } else {
                if (lastIs == 'period') {
                    arrThoiGian.push(lastRange);
                    lastIs = 'range';
                } else {
                    lastIs = 'period';
                }
            }
            arrThoiGian.push(e)
        });
        data.push({
            lopHocPhan, thoiGian: arrThoiGian, diaDiem
        })
    })

    var body = {
        access_token: '{{$access_token}}',
        calendar_name: 'HK1 2018-2019',
        data: data
    }

    console.log("Đang tạo lịch, vui lòng chờ (khoảng 15-20s)")

    fetch('{{$ajax_url}}', {
        method: "POST", // *GET, POST, PUT, DELETE, etc.
        mode: "cors", // no-cors, cors, *same-origin
        headers: {
            "Content-Type": "application/json; charset=utf-8",
        },
        body: JSON.stringify(body), // body data type must match "Content-Type" header
    })
        .then(response => response.json()) // parses response to JSON
        .then(resJson => {
            if (resJson.success) {
                location.href = resJson.url
            }
        })
}