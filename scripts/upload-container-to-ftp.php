<?php
$ftp_server = 'grugnino.esy.es';
$ftp_user_name = 'u989886342';
$ftp_password = 'Grugnetto8!';

$ssl = true;
$port = 65002;


$connection = ssh2_connect(ftp_server, 22);
ssh2_auth_password($connection, 'username', 'password');

$sftp = ssh2_sftp($connection);

exit();

if ($ssl == true) { // Is it and SSL Connection
    $conn_id = ftp_ssl_connect($ftp_server, intval($port)); // Create FTP Secure Connection
} else {
    $conn_id = ftp_connect($ftp_server, intval($port)); // Create FTP  Connection
}

$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_password); //Login in with credentials

if ((!$conn_id) || (!$login_result)) { // If login fails
    echo "FTP Connection failed to server $ftp_server for user $ftp_user_name <br>\r\n";
    exit;
} else {
    echo "Connected to Server $ftp_server, for user $ftp_user_name <br>\n";
}

ftp_close($conn_id); // Close FTP Connection

function make_directory($ftp_stream, $dir){ //Create FTP directory if not exists
    // if directory already exists or can be immediately created return true
    if (ftp_chdir ($ftp_stream, $dir) || @ftp_mkdir($ftp_stream, $dir)) return true;
    // otherwise recursively try to make the directory
    if (!make_directory($ftp_stream, dirname($dir))) return false;
    // final step to create the directory
    return ftp_mkdir($ftp_stream, $dir);
}



/*ftp_pasv($conn_id, true); // Set Passive mode

$recursiveFileResearch = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)); // Get all files in folder and subfolder in the selected directory

$files = array();

foreach ($recursiveFileResearch as $file) {

    if ($file->isDir()){
        continue;
    }

    $files[] = str_replace($dir . "/", "", str_replace('\\', '/', $file->getPathname())); // Store the file without backslashes (Windows..) and without the root directory

}

if (count($files) > 0) {
    foreach ($files as $file) {
        make_directory($conn_id, $remoteDir . dirname($file)); // Create directory if not exists
        ftp_chdir ($conn_id, $remoteDir . dirname($file)); // Go to that FTP directory
        echo "Current directory : " . ftp_pwd($conn_id) . " for file : " . basename($file)
            . " that could be found locally : " . $dir . "/" . $file . "<br>\n"; // Some logs to chekc the process
        ftp_put($conn_id, basename($file), $dir . "/"  . $file, FTP_BINARY); //Upload the file to current FTP directory
        echo "Uploaded " . basename($file) . "<br>\n"; // Some logs to chekc the process
    }
} else {
    echo "Didn't found any folder/files to send in directory : " . $dir . "<br>\n";
}*/



echo "Finished <br>\n";
?>