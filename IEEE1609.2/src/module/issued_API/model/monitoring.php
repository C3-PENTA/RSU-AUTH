<?php
namespace module\systemInfo\model;

class monitoring
{

	private $cpu = array();
	private $mem = array();
	private $storage_system = array();
	private $storage_data = array();
	private $storage_log = array();
	private $process = array();


	public function __construct()
	{
		### cpu by vmstat
		$cpu = `vmstat -wn -S M 1 2 | tail -n 1`;	
		// 숫자0을 빈배열로 판단해 array_filter시 삭제되므로, a로 치환 
		$cpu = str_replace(" 0", " a", $cpu);
		$cpu = explode(" ", $cpu);
		$this->cpu = array_values(array_filter($cpu));

		### memory by meminfo
		$mem = `free -m`;
		// 숫자0을 빈배열로 판단해 array_filter시 삭제되므로, a로 치환 
		$mem = str_replace(" 0", " a", $mem);
		$mem = explode("\n", $mem);
		$this->mem = $mem;

		### storage by df
		$storage_system = `df -h / | tail -n 1`;
		$storage_system = str_replace(" 0", " a", $storage_system); 
		$storage_system = explode(" ", $storage_system);
		$this->storage_system = array_values(array_filter(array_map("trim", $storage_system)));
		
		if(isset($_SERVER["SERVICE_MODE"]) && $_SERVER["SERVICE_MODE"] == "release")
			$storage_data = `df -h /opt/data | tail -n 1`;
		else
			$storage_data = `df -h / | tail -n 1`;

		$storage_data = str_replace(" 0", " a", $storage_data); 
		$storage_data = explode(" ", $storage_data);
		$this->storage_data = array_values(array_filter(array_map("trim", $storage_data)));

		if(isset($_SERVER["SERVICE_MODE"]) && $_SERVER["SERVICE_MODE"] == "release")
			$storage_log = `df -h /var/log | tail -n 1`;
		else
			$storage_log = `df -h / | tail -n 1`;
			
		$storage_log = str_replace(" 0", " a", $storage_log); 
		$storage_log = explode(" ", $storage_log);
		$this->storage_log = array_values(array_filter(array_map("trim", $storage_log)));

		### process
		$process = `ps -eo user,pid,ppid,rss,size,vsize,pmem,pcpu,comm --sort -rss | head -n 11`;
		$process = explode("\n", $process);
		$this->process = $process;

	}

	public function getMeminfo()
	{

		$mem  = $this->mem[1];
		$swap = $this->mem[2];

		$mem_info = explode(" ", $mem);
		$mem_info = array_values(array_filter(array_map("trim", $mem_info)));
		foreach($mem_info as $key => $val){
			if($val == "a"){
				$mem_info[$key]	= 0;
			}	
		}

		$swap_info = explode(" ", $swap);
		$swap_info = array_values(array_filter(array_map("trim", $swap_info)));
		foreach($swap_info as $key => $val){
			if($val == "a"){
				$swap_info[$key] = 0;
			}	
		}


		$req = array(
			 "mem_total"  		=> round($mem_info[1]/1024, 2)
			,"mem_used"	  		=> round($mem_info[2]/1024, 2)
			,"mem_used_rate" 	=> round(($mem_info[2]/$mem_info[1]) * 100)
			,"mem_free"			=> round($mem_info[3]/1024, 2)
			,"mem_free_rate"	=> round(($mem_info[3]/$mem_info[1]) * 100)
			,"mem_shared"		=> $mem_info[4]
			,"mem_shared_rate"	=> round(($mem_info[4]/$mem_info[1]) * 100)
			,"mem_buff"			=> $mem_info[5]
			,"mem_buff_rate"	=> round(($mem_info[5]/$mem_info[1]) * 100)
			,"mem_available"	=> $mem_info[6]
			,"mem_available_rate"=> round(($mem_info[6]/$mem_info[1]) * 100)
			,"swap_total" 		=> $swap_info[1]
			,"swap_used"  		=> $swap_info[2]
			,"swap_used_rate" 	=> @round(($swap_info[2]/$swap_info[1]) * 100)
			,"swap_free"  		=> $swap_info[3]	
			,"swap_free_rate"  	=> @round(($swap_info[3]/$swap_info[1]) * 100)
		);

		return $req;
	}

	public function getCPUinfo()
	{
		$cpu_us   = ($this->cpu[12] != "a")?$this->cpu[12]:0;
		$cpu_sy	  = ($this->cpu[13] != "a")?$this->cpu[13]:0;
		$cpu_idle = ($this->cpu[14] != "a")?$this->cpu[14]:0;
		$cpu_wa	  = ($this->cpu[15] != "a")?$this->cpu[15]:0;
		$cpu_st   = ($this->cpu[16] != "a")?$this->cpu[16]:0;

		$req = array(
			 "us"   => $cpu_us
			,"sy"   => $cpu_sy
			,"idle" => $cpu_idle
			,"wa"   => $cpu_wa
			,"st"   => $cpu_st
		);

		return $req;
	}

	public function getStorageinfo()
	{
		$req_system = array(
			 "size"  => $this->storage_system[1]
			,"used"  => $this->storage_system[2]
			,"avail" => $this->storage_system[3]
			,"perc"  => str_replace("%", "", $this->storage_system[4])
		);
		$req_data = array(
			 "size"  => @$this->storage_data[1]
			,"used"  => @$this->storage_data[2]
			,"avail" => @$this->storage_data[3]
			,"perc"  => @str_replace("%", "", $this->storage_data[4])
		);
		$req_log = array(
			 "size"  => @$this->storage_log[1]
			,"used"  => @$this->storage_log[2]
			,"avail" => @$this->storage_log[3]
			,"perc"  => @str_replace("%", "", $this->storage_log[4])
		);

		$req = array(
			 "req_system" => $req_system
			,"req_data" => $req_data
			,"req_log" => $req_log
		);

		return $req;
	}

	public function getProcessList()
	{
		$process = $this->process;

		for($i=1; $i<count($process); $i++){
			$process[$i] = str_replace("      ", " ", $process[$i]);
			$process[$i] = str_replace("     ", " ", $process[$i]);
			$process[$i] = str_replace("    ", " ", $process[$i]);
			$process[$i] = str_replace("   ", " ", $process[$i]);
			$process[$i] = str_replace("  ", " ", $process[$i]);

			$item = explode(" ", $process[$i]);

			if(count($item) < 8) continue;

			$req[] = array(
				"user"	=> $item[0],
				"pid"	=> $item[1],
				"ppid"	=> $item[2],
				"rss"	=> number_format(round($item[3]/1024, 2)),
				"size"	=> $item[4],
				"zsz"	=> $item[5],
				"mem"	=> $item[6],
				"cpu"	=> $item[7],
				"cmd"	=> $item[8],
			);
			
		}

		return $req;
	}
}
