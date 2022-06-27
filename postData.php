<?php
    $connect = mysqli_connect("localhost","root","","tugas_sk");

    $gasbaru = $_GET['gas'];

    mysqli_query($connect, "ALTER TABLE sensor AUTO_INCREMENT=1");

    $save = mysqli_query($connect, "insert into sensor(sensor1)
        values('$gasbaru')");

    if($save)
        echo "Berhasil Dikirim";
    else
        echo "Gagal Dikirim";

?>