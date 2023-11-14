<?
function clean_mysqli_connection( $dbc )
{
    while( mysqli_more_results($dbc) )
    {
        if(mysqli_next_result($dbc))
        {
            $result = mysqli_use_result($dbc);
            
            if( get_class($result) == 'mysqli_stmt' )
            {
                mysqli_stmt_free_result($result);
            }
            else
            {
                unset($result);
            }
        } 
    }
} 
?>