var sct = document.createElement('script')
sct.src = 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js'
document.querySelector('body').append(sct);
sct.onload = function (e) {
    var list = Array.from($('#tblCourseList tr'))
    list.splice(0, 1)
    list.splice(list.length - 1, 1)

    var data = list.map(function (tr) {
        var listTD = Array.from($(tr).find('td'));
        var lopHocPhan = $(listTD[2]).text().trim();
        var ngayThi = $(listTD[4]).text().trim();
        var caThi = $(listTD[5]).text().trim().match(/\d{2}:\d{2}/)[0];
        var diaDiem = $(listTD[8]).text().trim();
        return {
            lopHocPhan, ngayThi, caThi, diaDiem
        }
    });

    var body = {
        access_token: '{{$access_token}}',
        calendar_name: 'Thi HK1 2018-2019',
        data: data,
        ajax_type: 'lich_thi'
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