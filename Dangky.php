<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Đăng ký</title>
</head>
<body>
    <form action="XuLyDangKy.php" method="POST">
    <div class="khung">
        <h2>Đăng ký tài khoản</h2>
        <input type="text" name="name" placeholder="Họ tên" required>
        <br><br>
        <input type="email" name="email" placeholder="Email" required>
        <br><br>
        <input type="date" name="birthdate">
        <br><br>
        <input type="password" name="password" placeholder="Mật khẩu" required>
        <br><br>
        <input type="password" name="re-password" placeholder="Nhập lại mật khẩu" required>
        <br><br>
        <input type="text" name="address" placeholder="Địa chỉ" required>
        <br><br>
        <input type="text" name="phone" placeholder="Số điện thoại" required>
        <br><br>
        <div class="dk">
            <button type="submit" class="dk">Đăng ký</button>
        </div>
        <p>Đã có tài khoản <a href="Dangnhap.php"> Đăng nhập</a></p>
    </div>
</form>
        
    <style>
        body{
            background-color: aliceblue;
            display:flex;
            justify-content:center;
            align-items:center;
            height:100vh;
        }
        .khung{
            width:500px;
            margin:60px auto;
            border:1px solid #ccc;
            padding:20px;
            border-radius:8px;
            background:white;
        }
        h2{
            text-align:center;
        }
        input{
        width:100%;
        padding:10px;
        border:1px solid #ccc;
        border-radius:4px;
    }
    .dk{
        width: 100%;
        background-color: orangered;
        color: rgb(255, 255, 255);
        border-radius:5px;
    }
    .dk:hover{
    background:#716f6f;
    cursor:pointer;
    }
    </style>
<!-- Code injected by live-server -->
<script>
	// <![CDATA[  <-- For SVG support
	if ('WebSocket' in window) {
		(function () {
			function refreshCSS() {
				var sheets = [].slice.call(document.getElementsByTagName("link"));
				var head = document.getElementsByTagName("head")[0];
				for (var i = 0; i < sheets.length; ++i) {
					var elem = sheets[i];
					var parent = elem.parentElement || head;
					parent.removeChild(elem);
					var rel = elem.rel;
					if (elem.href && typeof rel != "string" || rel.length == 0 || rel.toLowerCase() == "stylesheet") {
						var url = elem.href.replace(/(&|\?)_cacheOverride=\d+/, '');
						elem.href = url + (url.indexOf('?') >= 0 ? '&' : '?') + '_cacheOverride=' + (new Date().valueOf());
					}
					parent.appendChild(elem);
				}
			}
			var protocol = window.location.protocol === 'http:' ? 'ws://' : 'wss://';
			var address = protocol + window.location.host + window.location.pathname + '/ws';
			var socket = new WebSocket(address);
			socket.onmessage = function (msg) {
				if (msg.data == 'reload') window.location.reload();
				else if (msg.data == 'refreshcss') refreshCSS();
			};
			if (sessionStorage && !sessionStorage.getItem('IsThisFirstTime_Log_From_LiveServer')) {
				console.log('Live reload enabled.');
				sessionStorage.setItem('IsThisFirstTime_Log_From_LiveServer', true);
			}
		})();
	}
	else {
		console.error('Upgrade your browser. This Browser is NOT supported WebSocket for Live-Reloading.');
	}
	// ]]>
</script>
</body>

</html>