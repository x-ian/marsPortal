<?    
    // In this class, array instead of string would be the standard input / output format.
    
    // Legacy way to add a job:
    // $output = shell_exec('(crontab -l; echo "'.$job.'") | crontab -');
    
    function stringToArray($jobs = '') {
        $array = explode("\n", trim($jobs)); // trim() gets rid of the last \r\n
        foreach ($array as $key => $item) {
            if ($item == '') {
                unset($array[$key]);
            }
        }
        return $array;
    }
    
    function arrayToString($jobs = array()) {
        $string = implode("\n", $jobs);
        return $string;
    }
    
    function getJobs() {
        $output = shell_exec('crontab -l');
        return stringToArray($output);
    }
    
    function saveJobs($jobs = array()) {
        $output = shell_exec('echo "'.arrayToString($jobs).'" | crontab -');
        return $output;	
    }
    
    function doesJobExist($job = '') {
        $jobs = getJobs();
        if (in_array($job, $jobs)) {
            return true;
        } else {
            return false;
        }
    }
    
    function addJob($job = '') {
        if (doesJobExist($job)) {
            return false;
        } else {
            $jobs = getJobs();
            $jobs[] = $job;
            return saveJobs($jobs);
        }
    }
    
    function removeJob($job = '') {
        if (doesJobExist($job)) {
            $jobs = getJobs();
            unset($jobs[array_search($job, $jobs)]);
            return saveJobs($jobs);
        } else {
            return false;
        }
    }
    
?>