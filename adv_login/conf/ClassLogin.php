<?php
class Login {
    var $db;

    public function __construct(){
        global $db;
        // Menghubungkan variabel database $db ke class Login
        $this->db = $db;
    }

    public function cek_login(){
        /* Kondisi user dinyatakan login adalah:
        1. Memiliki $_COOKIE['adv_token']
        2. $_COOKIE['adv_token'] terdaftar di tabel tb_admin_log, dan belum expired
        3. IP dan User Agent sesuai dengan token yang terdaftar
        */
        if(isset($_COOKIE['adv_token'])){
            $token = $_COOKIE['adv_token'];
            $now = date("Y-m-d H:i:s");
            $cek = $this->db->query("SELECT * FROM tb_admin_log WHERE token = ".$this->db->quote($token)." AND expired > ".$this->db->quote($now));
            
            if($cek && $cek->rowCount() > 0){
                // Kalau token di cookie tersebut ada, lakukan pengecekan IP dan User Agent
                $row = $cek->fetch();
                if($row['ip'] == $_SERVER['REMOTE_ADDR'] || $row['useragent'] == $_SERVER['HTTP_USER_AGENT']){
                    $username = $row['username'];
                    $get_admin = $this->db->query("SELECT * FROM tb_admin WHERE user = ".$this->db->quote($username));
                    $rget = $get_admin->fetch();

                    return array(
                        "username" => $rget['user'],
                        "name" => $rget['name'],
                        "email" => $rget['email'],
                        "priviledge" => $rget['priviledge']
                    );
                }
            }
        }
        return false;
    }

    public function salah_login_action($username){
        // Dipanggil saat user salah memasukkan username/password (dicatat dengan STAT = 0)
        $tgl = date("Y-m-d H:i:s");
        $ip = $_SERVER['REMOTE_ADDR'];
        $useragent = $_SERVER['HTTP_USER_AGENT'];

        $save = $this->db->prepare("INSERT INTO tb_admin_log VALUES (NULL, ?, '', '', ?, ?, ?, 0)");
        $save->execute(array(
            $tgl, $username, $ip, $useragent
        ));
        return true;
    }

    public function cek_salah_login($limit=5){
        // Cek apakah di tabel tb_admin_log ada 5 IP yang sama dalam keadaan salah login (STAT = 0)
        $ip = $_SERVER['REMOTE_ADDR'];
        $cek = $this->db->prepare("SELECT * FROM tb_admin_log WHERE stat = 0 AND ip = ?");
        $cek->execute(array($ip));
        
        if($cek->rowCount() >= $limit)
            return false;
        return true;
    }

    public function true_login($username, $expired){
        // Dipanggil ketika username dan password sudah tepat dimasukkan
        $tgl = date("Y-m-d H:i:s");
        if($expired <> 0){
            // Kalau remember me dicentang, expire 1 tahun dari sekarang
            $expireddb = date("Y-m-d H:i:s", strtotime($expired));
        } else {
            // Kalau tidak dicentang, default 6 jam
            $expireddb = date("Y-m-d H:i:s", strtotime("+6 hours"));
        }

        $ip = $_SERVER['REMOTE_ADDR'];
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        $token = sha1($ip.$expireddb."string_random_apasaja".microtime());

        // Tandai kesalahan login sebelumnya pada IP & User Agent yang sama dengan mengubah STAT dari 0 menjadi 9
        $upd = $this->db->query("UPDATE tb_admin_log SET stat = 9 WHERE token = '' AND ip = ".$this->db->quote($ip)." AND useragent = ".$this->db->quote($useragent));

        // Memasukkan data lengkap ke tb_admin_log dengan flag STAT = 1
        $save = $this->db->prepare("INSERT INTO tb_admin_log VALUES (NULL, ?, ?, ?, ?, ?, ?, 1)");
        $save->execute(array(
            $tgl, $expireddb, $token, $username, $ip, $useragent
        ));

        // Simpan token ke cookie
        $expr = 0;
        if($expired <> 0){
            $expr = intval(strtotime($expired));
        }
        setcookie("adv_token", $token, $expr, "/");

        return true;
    }

    public function logout(){
        // Dipanggil saat user logout dari sistem
        if(isset($_COOKIE['adv_token'])){
            $token = $_COOKIE['adv_token'];
            $now = date("Y-m-d H:i:s");
            
            unset($_COOKIE['adv_token']);
            setcookie("adv_token", "", time() - 3600, "/"); // Menghapus cookie di browser
            
            // Update expired di database supaya token yang sudah logout tidak bisa di-hijack
            $this->db->query("UPDATE tb_admin_log SET expired = ".$this->db->quote($now)." WHERE token = ".$this->db->quote($token));
        }
        return true;
    }

    public function login_redir(){
        // Mengecek apabila user tidak memiliki akses, langsung diredirect ke halaman login
        if(!$this->cek_login()){
            header("location:index.php");
            exit();
        }
    }
}
?>