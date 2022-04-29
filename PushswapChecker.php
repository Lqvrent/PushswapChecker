<?php
/*
** EPITECH Script, 2022
** PushswapChecker
** File description:
** Script to check the pushswap algorithm
*/

$config = [
    'list_sizes' => [3, 4, 5, 10, 20],
    'timeout' => 5, // Limit the runtime of the Pushswap (in seconds)
    'pushswap_filename' => 'pushswap', // Web@cadémie : "push_swap.php" // PGE1 : "pushswap"
    'is_php_script' => false, // Web@cadémie : true // PGE1 : false
];

class PushswapChecker {
    private $stack_ref;
    private $stack_a;
    private $stack_b;
    private $tests_results = [];

    public function __construct() {
        $this->stack_ref = [];
        $this->stack_a = [];
        $this->stack_b = [];
    }

    public function reset() {
        $this->stack_ref = [];
        $this->stack_a = [];
        $this->stack_b = [];
    }

    private function sa() {
        if (empty($this->stack_a) || count($this->stack_a) < 2)
            return;
        $tmp = $this->stack_a[0];
        $this->stack_a[0] = $this->stack_a[1];
        $this->stack_a[1] = $tmp;
    }

    private function sb() {
        if (empty($this->stack_b) || count($this->stack_b) < 2)
            return;
        $tmp = $this->stack_b[0];
        $this->stack_b[0] = $this->stack_b[1];
        $this->stack_b[1] = $tmp;
    }

    private function sc() {
        $this->sa();
        $this->sb();
    }

    private function pa() {
        if (empty($this->stack_b))
            return;
        array_unshift($this->stack_a, array_shift($this->stack_b));
    }

    private function pb() {
        if (empty($this->stack_a))
            return;
        array_unshift($this->stack_b, array_shift($this->stack_a));
    }

    private function ra() {
        if (empty($this->stack_a))
            return;
        array_push($this->stack_a, array_shift($this->stack_a));
    }

    private function rb() {
        if (empty($this->stack_b))
            return;
        array_push($this->stack_b, array_shift($this->stack_b));
    }

    private function rr() {
        $this->ra();
        $this->rb();
    }

    private function rra() {
        if (empty($this->stack_a))
            return;
        array_unshift($this->stack_a, array_pop($this->stack_a));
    }

    private function rrb() {
        if (empty($this->stack_b))
            return;
        array_unshift($this->stack_b, array_pop($this->stack_b));
    }

    private function rrr() {
        $this->rra();
        $this->rrb();
    }

    private function check_algorithm($output) {
        $output = trim($output);
        if (empty($output))
            return;
        $operations = explode(" ", $output);
        foreach ($operations as $operation) {
            switch ($operation) {
                case 'sa':
                    $this->sa();
                    break;
                case 'sb':
                    $this->sb();
                    break;
                case 'sc':
                    $this->sc();
                    break;
                case 'pa':
                    $this->pa();
                    break;
                case 'pb':
                    $this->pb();
                    break;
                case 'ra':
                    $this->ra();
                    break;
                case 'rb':
                    $this->rb();
                    break;
                case 'rr':
                    $this->rr();
                    break;
                case 'rra':
                    $this->rra();
                    break;
                case 'rrb':
                    $this->rrb();
                    break;
                case 'rrr':
                    $this->rrr();
                    break;
                default:
                    print("Invalid operation: '$operation'\n");
                    return;
            }
        }
    }

    private function isSorted($stack = null) {
        $stack = $stack ?: $this->stack_a;
        $last = reset($stack);
        $isSorted = true;

        foreach ($stack as $value) {
            if ($last > $value) {
                $isSorted = false;
                break;
            }
            $last = $value;
        }
        return $isSorted;
    }

    public function launch_test($list_size) {
        $this->reset();
        print("\e[90m\e[1m-======- Starting test with list of size $list_size -======-\e[0m\n");
        $this->stack_ref = range(1, $list_size);
        while ($this->isSorted($this->stack_ref)) {
            shuffle($this->stack_ref);
        }
        $this->stack_a = $this->stack_ref;
        $this->stack_b = [];
        $this->launch_program();
        // print("\e[90m\e[1m-======- Endding test with list of size $list_size -======-\e[0m\n\n");
    }

    public function launch_program() {
        global $config;
        $output = "";
        $exit_code = -1;
        $cmd = ($config["is_php_script"] ? "php " : "./") . $config["pushswap_filename"] . " " . implode(" ", $this->stack_ref);
        // print("Running command: $cmd\n");
        $descriptor = array(
            0 => array("pipe", "r"),
            1 => array("pipe", "w"),
            2 => array("pipe", "w"),
        );
        $process = proc_open($cmd, $descriptor, $pipes);
        // timeout handling
        $timeout = $config["timeout"];
        $start_time = time();
        while ($exit_code === -1) {
            $status = proc_get_status($process);
            if ($status["running"] === false) {
                $output .= stream_get_contents($pipes[1]);
                fclose($pipes[1]);
                $exit_code = $status["exitcode"];
                break;
            }
            // check timeout
            if (time() - $start_time > $timeout) {
                print("Timed out\n");
                proc_terminate($process);
                $exit_code = -1;
                break;
            }
        }
        if ($exit_code === 0) {
            $this->check_algorithm($output);
        }
        $this->tests_results[] = [
            "hasTimeout" => $exit_code == -1,
            "hasCrashed" => $exit_code != 0,
            "isSorted" => ($exit_code == -1 || $exit_code != 0) ? false : $this->isSorted(),
        ];
        // print result
        if ($exit_code == -1) {
            print("\e[31mKO: Timed out after $timeout seconds\n");
        }
        else if ($exit_code != 0) {
            print("\e[31mKO: Crashed\n");
        }
        else if ($this->tests_results[count($this->tests_results) - 1]["isSorted"]) {
            print("\e[32m\e[1mOK\n");
        }
        else {
            print("\e[31mKO: Not sorted\n");
            print("\nDiff:\n");
            print("- Reference:     " . implode(" ", $this->stack_ref) . "\n");
            print("- After your PS: " . implode(" ", $this->stack_a) . "\n");
        }
        print("\e[0m\n");
    }

    public function synthesis() {
        global $config;
        $nb_tests = count($this->tests_results);
        $nb_success = 0;
        $nb_fail = 0;
        $nb_timeout = 0;
        $nb_crashed = 0;
        foreach ($this->tests_results as $test) {
            if ($test["hasTimeout"]) {
                $nb_timeout++;
            } elseif ($test["hasCrashed"]) {
                $nb_crashed++;
            } elseif ($test["isSorted"]) {
                $nb_success++;
            } else {
                $nb_fail++;
            }
        }
        print("\e[1m-=- Results -=-\e[0m\n");
        if ($nb_timeout > 0)
            print("\e[31mTimed out ($config[timeout] secs): $nb_timeout/$nb_tests\e[0m\n");
        if ($nb_crashed > 0)
            print("\e[31mCrashed: $nb_crashed/$nb_tests\e[0m\n");
        if ($nb_fail > 0)
            print("\e[31m");
        print("Fail: $nb_fail/$nb_tests\e[0m\n");
        print("\e[0m");
        if ($nb_success > 0)
            print("\e[32m");
        print("Success: $nb_success/$nb_tests\e[0m\n");
    }
}

if (!file_exists($config["pushswap_filename"])) {
    print("Error: " . $config["pushswap_filename"] . " does not exist.\n");
    exit(84);
}

$checker = new PushswapChecker();
foreach ($config['list_sizes'] as $list_size) {
    $checker->launch_test($list_size);
}
$checker->synthesis();
?>
