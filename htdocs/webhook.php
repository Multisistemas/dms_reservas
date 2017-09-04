<?php
// Use in the "Post-Receive URLs" section of your GitHub repo.
if ( $_POST['payload'] ) {
  $cwd = getcwd();
  shell_exec( "cd $cwd && git reset --hard HEAD && git pull && chmod -R 755 *" );
}
?>hi