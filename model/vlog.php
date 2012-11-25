<?php

$oVlog = json_decode(file_get_contents('./js/vlog.json'));
#print_r($oVlog->feed->entry[0]->content->{'$t'});
#print_r($oVlog->feed->entry[0]);
print_r($oVlog->feed->entry[0]->link[0]->href);
?>