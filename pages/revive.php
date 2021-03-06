<?php
/*
           ___           __ _           _ __    __     _
          / __\ __ __ _ / _| |_ ___  __| / / /\ \ \___| |__
         / / | '__/ _` | |_| __/ _ \/ _` \ \/  \/ / _ \ '_ \
        / /__| | | (_| |  _| ||  __/ (_| |\  /\  /  __/ |_) |
        \____/_|  \__,_|_|  \__\___|\__,_| \/  \/ \___|_.__/
                          --[ Build 1.5 ]--
                    - coded and revised by Faded -

    CraftedWeb is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This is distributed in the hope that it will be useful, but
    WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    included license for more details.

    Support/FAQ #EmuDevs - http://emudevs.com
*/

    $sql = new mysqli($GLOBALS['connection']['host'],$GLOBALS['connection']['user'],$GLOBALS['connection']['password']);
    echo "<div class='box_two_title'>Character Reviver</div> Choose the character you wish to revive. The character will be revived with 1 health.<hr/>";
    $service = "revive";
    if($GLOBALS['service'][$service]['price'] == 0)
        echo '<span class="attention">Revive is free of charge.</span>';
    else
    {
        echo '<span class="attention">Revive costs';
        echo $GLOBALS['service'][$service]['price'].' '.website::convertCurrency($GLOBALS['service'][$service]['currency']);
        echo "</span>";
        if($GLOBALS['service'][$service]['currency']=="vp")
            echo "<span class='currency'>Vote Points: ".account::loadVP($_SESSION['cw_user'])."</span>";
        elseif($GLOBALS['service'][$service]['currency']=="dp")
            echo "<span class='currency'>".$GLOBALS['donation']['coins_name'].": ".account::loadDP($_SESSION['cw_user'])."</span>";
    }

    account::isNotLoggedIn();
    $sql->select_db($GLOBALS['connection']['webdb']);
    $num = 0;
    $result = $sql->query('SELECT char_db,name FROM realms ORDER BY id ASC');
    while($row = mysqli_fetch_assoc($result))
    {
        $acct_id = account::getAccountID($_SESSION['cw_user']);
        $realm = $row['name'];
        $char_db = $row['char_db'];
        $sql->select_db($char_db);
        $result = $sql->query('SELECT name,guid,gender,class,race,level,online FROM characters WHERE account='.$acct_id);
        while($row = mysqli_fetch_assoc($result))
        {
            ?><div class='charBox'>
            <table width="100%">
            <tr>
            <td width="73">
            <?php
            if(!file_exists('styles/global/images/portraits/'.$row['gender'].'-'.$row['race'].'-'.$row['class'].'.gif'))
                echo '<img src="styles/'.$GLOBALS['template']['path'].'/images/unknown.png" />';
            else
                echo "<img src='styles/global/images/portraits/".$row['gender']."-".$row['race']."-".$row['class'].".gif' border='none'>";
            ?>
            </td>

            <td width="160"><h3><?php echo $row['name']; ?></h3>
            <?php echo $row['level']." ".character::getRace($row['race'])." ".character::getGender($row['gender']).
            " ".character::getClass($row['class']); ?>
            </td>

            <td>Realm: <?php echo $realm; ?>
            <?php
            if($row['online'] == 1)
                echo "<br/><span class='red_text'>Please log out before trying to unstuck.</span>";
            ?>
            </td>

            <td align="right"> &nbsp; <input type="submit" value="Revive"
            <?php if($row['online'] == 0) { ?>
                onclick='revive(<?php echo $row['guid']; ?>,"<?php echo $char_db; ?>")' <?php }
            else { echo 'disabled="disabled"'; } ?>>
            </td>
            </tr>
            </table>
            </div>
            <?php
            $num++;
        }
    }