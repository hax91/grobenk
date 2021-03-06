<script src="http://code.jquery.com/jquery-latest.js"></script>

<SCRIPT language="javascript">
    $(function(){
        $("#select_all").click(function () {
            $('.case').prop('checked', this.checked);
        });
        $(".case").click(function(){
            if($(".case").length == $(".case:checked").length) {
                $("#select_all").prop("checked", "checked");
            } else {
                $("#select_all").removeProp("checked");
            }
        });
    });
    function myFunction() {
        document.getElementById("alert").style.display = "none";
    }
</SCRIPT>

<?php
require_once ("dbconnect.php");
session_start();
mysqli_set_charset($conn,"utf8");
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    header("Location:odjava.php");
}
$_SESSION['LAST_ACTIVITY'] = time();
$error = 0;
if (!$_SESSION['admin_loggedin']) header("Location:denied_permission.php");
echo '
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>BloodBank</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
        <script src="https://use.fontawesome.com/releases/v5.0.8/js/all.js"></script>
        <link href="style.css" rel="stylesheet">
        <link href="adminstyle.css" rel="stylesheet">
    </head>';
echo "
    <div id='nav-placeholder' onload>
    </div> 
    <script>
    $(function(){
      $('#nav-placeholder').load('adminnavbar.php');
    });
    </script>";
echo '
<div class="admin-content">
        <ul class="nav nav-tabs" id="myTab" style="width:950px;margin-left: -140px">
            <li class="nav-item">
                <a class="nav-link" href="eventi.php?keyword=&trazi=Traži">Eventi</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="zahtjevi.php">Zahtjevi</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="dodajbolnicu.php">Dodaj bolnicu</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link active" href="donacije.php?keyword=&trazi=Traži">Donacije</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="obavijesti.php">Obavijesti</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="statistika.php">Statistika</a>
            </li>
        </ul>
</div>';
echo "
<div id='supplies' onload></div>
<script>
$(function(){
  $('#supplies').load('zalihe.php');
});
</script>"
;
echo '
<div style="margin-left:20%" class="col-md-8">';

$date = date("Y-m-d");
$query = "select lokacija.naziv_lokacije, donor.OIB_donora, donor.ime_prezime_donora, donor.krvna_grupa_don from lokacija, donor, moj_event
              where lokacija.datum_dogadaja = '$date' and lokacija.id_lokacije = moj_event.id_lokacije and donor.OIB_donora = moj_event.OIB_donora_don and moj_event.prisutnost = '0'";
$run = mysqli_query($conn, $query);
if ($result = mysqli_num_rows($run) != 0) {
    echo'<div id="content30" class="toggle"  ><br><br>';
    echo '<form action="" method="GET">
            <input type="text" class="eventi-pretrazi" name = "keyword" placeholder="Pretraži donacije">
            <input style="margin-left:10px;" type="submit" class="zbtn" name="trazi" value="Traži">
        </form>
        <br><span class="newevent">Nove donacije:</span><br>
    
        <form action="" method="GET">
<div id="table-wrapper">
    <div id="table-scroll">
        <table id="dond" class="event-t">
            <thead><tr>
                <th style="border-left: 2px solid #9F0A00;" class="tht">LOKACIJA</th>
                <th class="tht">OIB</th>
                <th class="tht">IME I PREZIME</th>
                <th class="tht">KRVNA GRUPA</th>
                <th style="border-right:2px solid #9F0A00;" class="tht">UNESI</th>
            </tr></thead>
        
            ';
    if (isset($_GET['trazi'])) {
        $pretraga = $_GET['keyword'];
        $date = date("Y-m-d");
        $query = "select lokacija.naziv_lokacije, donor.OIB_donora, donor.ime_prezime_donora, donor.krvna_grupa_don from lokacija, donor, moj_event
                          where lokacija.datum_dogadaja = '$date' and lokacija.id_lokacije = moj_event.id_lokacije and donor.OIB_donora = moj_event.OIB_donora_don and moj_event.prisutnost = '0'
                          and ((lokacija.naziv_lokacije like '%$pretraga%') or (donor.ime_prezime_donora like '%$pretraga%') or (donor.krvna_grupa_don = '$pretraga'))";
        $run = mysqli_query($conn, $query);
        $result = $run or die ("Failed to query database" . mysqli_error($conn));
        while ($row = mysqli_fetch_array($result)) {
            echo '
                            <tbody><tr class="don">
                                <td style="border-left: 2px solid #9F0A00;" border-left:none;">' . $row['naziv_lokacije'] . '</td>
                                <td> ' . $row['OIB_donora'] . '</td>
                                <td>' . $row['ime_prezime_donora'] . '</td>
                                <td>' . $row['krvna_grupa_don'] . '</td>
                                <td style="border-right:2px solid #9F0A00; padding-left:45px;"><input type="checkbox" class="case" name="check_list[]" value=' . $row['OIB_donora'] . '></td>
                            </tr><tbody>';
        }
        echo '</table></div></div>
            <br>
                    <input type="text" name="kolicina">&nbsp
                    <input type="submit" class="zbtn" name="doniraj" value="Unesi donaciju"><br><br>
                    <input type="submit" class="zbtn" name="odbij" value="Odbij donaciju"><br><br>
                    <span class="prevent">Označi sve&nbsp</span>
                    <input type="checkbox" name="select_all" id = "select_all">
                    
                </form>';
    }
    if (isset($_GET['doniraj'])) {
        $date = date("Ymd");
        $kol = $_GET['kolicina'];
        if ($kol<=0 or $kol>0.7) {
            $error = 1;

        } else {
            if (!empty($_GET['check_list'])) {
                foreach ($_GET['check_list'] as $OIB) {
                    $info = "select * from donor where OIB_donora = '$OIB'";
                    $run = mysqli_query($conn, $info);
                    $result = $run or die ("Failed to query database" . mysqli_error($conn));
                    $row = mysqli_fetch_array($result);
                    $krvna_grupa = $row['krvna_grupa_don'];
                    $lokacija = "select * from lokacija where datum_dogadaja = '$date' and
                                         id_lokacije in (select id_lokacije from moj_event where OIB_donora_don = '$OIB')";
                    $run = mysqli_query($conn, $lokacija);
                    $result = $run or die ("Failed to query database" . mysqli_error($conn));
                    $row = mysqli_fetch_array($result);
                    $id_lokacije = $row['id_lokacije'];
                    echo $id_lokacije;
                    //umetanje donacije u tablicu donacija
                    $sql = "INSERT into donacija (kolicina_krvi_donacije, krvna_grupa_zal, OIB_donora, idlokacija)
                                            values ( '$kol', '$krvna_grupa', '$OIB', '$id_lokacije')";
                    $run = mysqli_query($conn, $sql);
                    $result = $run or die ("Failed to query database" . mysqli_error($conn));
                    $sql = "UPDATE donor SET br_donacija = br_donacija+1 where OIB_donora = '$OIB'";
                    $run = mysqli_query($conn, $sql);
                    $result = $run or die ("Failed to query database" . mysqli_error($conn));
                    $sql = "UPDATE moj_event SET prisutnost = '1' WHERE OIB_donora_don = '$OIB' AND id_lokacije='$id_lokacije'";
                    $run = mysqli_query($conn, $sql);
                    $result = $run or die ("Failed to query database" . mysqli_error($conn));
                    $sql = "UPDATE zaliha set kolicina_grupe = kolicina_grupe + '$kol' where krvna_grupa = '$krvna_grupa'";
                    $run = mysqli_query($conn, $sql);
                    $result = $run or die ("Failed to query database" . mysqli_error($conn));
                    header("Location:donacije.php?keyword=&trazi=Traži");
                }
            }
        }
    }
    if ($error == 1) {
        echo 'Unesite ispravnu količinu krvi.';
    }
    if (isset($_GET['odbij'])) {
        $date = date("Ymd");
        if (!empty($_GET['check_list'])) {
            foreach ($_GET['check_list'] as $OIB) {
                $lokacija = "select * from lokacija where datum_dogadaja = '$date' and
                                     id_lokacije in (select id_lokacije from moj_event where OIB_donora_don = '$OIB')";
                $run = mysqli_query($conn, $lokacija);
                $result = $run or die ("Failed to query database" . mysqli_error($conn));
                $row = mysqli_fetch_array($result);
                $id_lokacije = $row['id_lokacije'];
                $sql = "UPDATE moj_event SET prisutnost = '-1' WHERE OIB_donora_don = '$OIB' and id_lokacije='$id_lokacije' and prisutnost = '0'";
                $run = mysqli_query($conn, $sql);
                $result = $run or die ("Failed to query database" . mysqli_error($conn));
                header("Location:donacije.php?keyword=&trazi=Traži");
            }
        }
    }
} else {
    echo 'neka poruka u stilu:<h3>Trenutno nema prijava za donaciju</h3>';
}
echo '
    <div style="height:200px;">
    </div>
    ';
?>