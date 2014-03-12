<?php
/**
 * FlatRep
 *
 * Flat Repository, Save any data to file (noSQL)
 *
 * @author 		OMAPS LABS Agus Prasetyo (agusprasetyo811@gmail.com)
 * @copyright	Copyright (c) 2013 - 2014, OMAPSLABS
 * @link		http://cmlocator.com
 * @filesource 	http://github.com/agusprasetyo811/flatrep
 * @since		Version 1.0
 *
 */

// ------------------------------------------------------------------------

/**
 * Flatrep Library Class
 *
 * Save any ada to file (noSQL)
 *
 * @subpackage	Libraries
 * @category	Libraries
 * @author		OMAPSLABS
 * @link 		http://cmlocator.com
 */


class Flatrep {
	
	private $CI, $get_search, $get_column, $file, $get_limit, $sort, $key_in;
	private $flatreptype = 'Lm9tYXBz';
	private $repo = '';
	private $key = '8053f01bda8b767dd96a34035505f30f';
	 
	
	public function __construct() {
		//$this->CI =& get_instance();
	}
	
	/**
	 * Flatrep Configuration
	 * 
	 * @param array $conf
	 */
	public function config($conf) {
		
		// if isset repo
		if (isset($conf['repo'])) {
			$this->repo = @$conf['repo'];
		}
		
		// if isset key
		if (isset($conf['key'])) {
			if ($conf['key'] != '') {
				$this->key = @$conf['key'];
			} else {
				exit('ERROR WHEN USE KEY. KEY MUST BE SET');
			}
		}
	}
	
	/**
	 * Search value of file with column
	 * 
	 * @param string $search
	 * @param string $column
	 */
	public function search($search, $column) {
		if ($search == NULL || $column == NULL) {
			exit('ERROR WHEN SEARCH');
		} else {
			$this->get_search = $search;
			$this->get_column = $column;
		}
	}
	
	public function get_key() {
		return $this->key;
	}
	
	public function get_repo() {
		return $this->repo;
	}
	
	public function get_search() {
		return  $this->get_search;
	}
	
	public function get_search_column() {
		return  $this->get_column;
	}

	
	/**
	 * Selecting File Repo
	 * 
	 * @param string $file
	 * @param string $path
	 */
	public function file($file, $path = NULL) {
		if ($file) {
			if ($path == NULL) {
				if ($this->repo != '') {
					$path = $this->repo;
				} else {
					exit("REPOSITORY NOT FOUND");
				}	
			}
			$this->file = $path.$file .base64_decode($this->flatreptype);
			
			if (!file_exists($this->file)) {
				write_to_file($this->file, $this->encode_file('[]', $this->key));	
			}
		} else {
			exit('OMAPS FILE NOT FOUND');
		}
		
	}
	
	public function get_file() {
		return $this->file;
	}
	
	
	public function get_insert_id() {
		
		if ($this->get_file() != NULL) {
			return $this->count_data() + 1;
		} else {
			exit('OAMPS FILE NOT FOUND WHEN GET ID');
		}
	}
	
	/**
	 * Sort data with key in
	 * 
	 * @param unknown_type $sort
	 * @param unknown_type $key_in
	 */
	public function sortir($sort, $key_in) {
		if ($sort != NULL || $key_in != NULL) {
			$this->sort = $sort;
			$this->key_in = $key_in;
		} else {
			exit('ERROR WHEN SORTIR SORT AND KEY IN NOT SET');
		}
		
	}
	
	public function get_sort() {
		return $this->sort;
	}
	
	public function get_key_in() {
		return $this->key_in;
	}
	
	/**
	 * Execute reading file 
	 * 
	 * @param string $sort
	 * @return string
	 */
	 
	public function read($sort = SORT_ASC) {
		$file = $this->get_file();
		
		if (!file_exists($file)) {
			exit('OMAPS FILE NOT EXISTS WHEN READ');
		}
		
		$file = reading_file($file);
		
		if (trim($file) == '') {
			return json_encode(array());
		} else {
			$file = ($this->get_limit != '') ? json_decode($this->decode_file($this->get_limit, $this->get_key())) : json_decode($this->decode_file($file, $this->get_key()), true);
			$search = $this->get_search();
			$column = $this->get_search_column();
				
				
			if ($search != NULL) {
				$get_index = $this->array_search_multidimention($search, $column, $file);
				foreach ($get_index as $row) {
					$get_search[] = @$file[$row];
				}
				$read = $get_search;
			} else {
				$read = $file;
			}
				
			$read = ($this->sort != NULL || $this->key_in != NULL) ? $this->sort_array_by_column($read, $this->key_in, $this->sort, SORT_NUMERIC) : $this->sort_array_by_column($read, '__repid', $sort, SORT_NUMERIC);
			return json_encode($read);
		}
	}
	
	/**
	 * Limited file into spesific count 
	 * 
	 * @param string $limit
	 * @param string $per_page
	 */
	public function limit($limit, $per_page = 0) {
		if ($limit != NULL ) {
			if ($this->get_file() != NULL) {
				$file = $this->reading_file($this->get_file());
				$lmt_prpe = json_decode($this->decode_file($file, $this->get_key()));
				if ($per_page != 0) {
					$this->get_limit = $this->encode_file(json_encode(array_slice($lmt_prpe, $limit, $per_page)), $this->get_key());
				} else {
					$this->get_limit = $this->encode_file(json_encode(array_slice($lmt_prpe, 0, $limit)), $this->get_key());
				}
			} else {
				exit('OMAPS FILE NOT EXISTS WHEN LIMIT');
			}
		} else {
			exit('ERROR WHEN LIMIT');	
		}
		
	}
	
	/**
	 * Count of file 
	 * 
	 * @return number
	 */
	public function count_data() {
		$file = $this->get_file();
		$file = $this->reading_file($file);
		
		if ($file) {
			$file = json_decode($this->decode_file($file, $this->get_key()), TRUE);
			$file = (is_array($file)) ? $file : array();
			return count($file);
		} else {
			return 0;
		}
	}
	
	
	/**
	 * Create Flatrep data
	 * 
	 * @param unknown_type $column
	 */
	public function create($flat_data) {
		if ($this->get_file()) {
			if (is_array($flat_data)) {
				$get_data = json_encode($flat_data);
				$data = $this->encode_file($get_data, $this->get_key());
				$this->write_to_file($this->get_file(), $data);
				return $this->reading_file($this->get_file());
			} else {
				exit('ERROR FLAT DATA');
			}	
		} else {
			exit('OMAPS FILE NOT FOUND WHEN CREATE');
		}
	}
	
	/**
	 * 
	 */
	public function delete($repid, $in) {
		if ($repid == NULL || $in == NULL) {
			exit('ERROR WHEN DELETE, REPID AND KEY IN NOT SET');
		} else {
			if ($this->get_file()) {
				$get_data = $this->reading_file($this->get_file());
				$data = json_decode($this->decode_file($get_data, $this->get_key()), TRUE);
				$get_index = $this->array_search_multidimention($repid, $in, $data);
				unset($data[$get_index[0]]);
				$this->write_to_file($this->get_file(), $this->encode_file(json_encode(array_values($data)), $this->get_key()));
				return  json_encode(array_values($data));
			} else {
				exit('OMAPS FILE NOT FOUND WHEN DELETE');
			}
			
		}
		
	}
	
	/**
	 * Decode file with blowfish algoritm
	 * 
	 * @param string $data
	 * @param sring $key
	 */
	public function decode_file($data, $key) {
		return base64_decode(@mcrypt_decrypt(MCRYPT_BLOWFISH, $key, $data, MCRYPT_MODE_CFB));
	}
	
	/**
	 * Encode file wih blowfish algoritm
	 * 
	 * @param string $data
	 * @param string $key
	 */
	public function encode_file($data, $key) {
		return @mcrypt_encrypt(MCRYPT_BLOWFISH, $key, base64_encode($data), MCRYPT_MODE_CFB);
	}
	
	/**
	 * 
	 * @param unknown_type $value_search
	 * @param unknown_type $key_in
	 * @param unknown_type $array
	 * @return unknown|NULL
	 */
	function array_search_multidimention($value_search, $key_in, $array) {
		foreach ($array as $key => $val) {
			$pos = strpos($val[$key_in], $value_search);
			if ($pos !== false) {
				$get_data[] = $key;
			}
		}
		
		// if data not found
		if (!isset($get_data)) {
			$get_data[] = array();
		}
		return $get_data;
	}
	
	/**
	 * 
	 * @param unknown_type $arr
	 * @param unknown_type $col
	 * @param unknown_type $dir
	 * @return mixed
	 */
	public function sort_array_by_column(&$arr, $col, $dir = SORT_ASC) {
		$arr = json_decode(json_encode($arr), TRUE);
		$sort_col = array();
		if ($arr != NULL || is_array($arr)) {
			
			foreach (@$arr as $key => $row) {
				@$sort_col[$key] = $row[$col];
			}
			@array_multisort($sort_col, $dir, $arr);
			return json_decode(json_encode($arr));
		} else {
			return json_decode($this->decode_file($this->reading_file($this->get_file()), $this->get_key()));
		}
	}
	
	/**
	 * 
	 * @param unknown_type $file_txt
	 * @param unknown_type $data
	 */
	public function write_to_file($file_txt,$data){
		$data_txt = $file_txt;
		if(!file_exists($data_txt)){
			$open = fopen($data_txt, "w");
			fputs($open,' ');
			fclose($open);
		}else{
			$open = fopen($data_txt, "w");
			fwrite($open,$data);
			fclose($open);
		}
	}
	
	/**
	 * 
	 * @param unknown_type $file_txt
	 * @return string
	 */
	public function reading_file($file_txt){
		$data_txt = $file_txt;
		$fh = fopen($data_txt, "r");
		$file = file_get_contents($data_txt);
		return $file;
	}
	
	/**
	 * 
	 * @param unknown_type $file
	 * @return boolean
	 */
	public function delete_file($file) {
		return unlink($file);
	}
	
	/**
	 * 
	 * @param unknown_type $dir
	 */
	public function delete_dir($dir) {
		if (!file_exists($dir)) return true;
		if (!is_dir($dir)) return unlink($dir);
		foreach (scandir($dir) as $item) {
			if ($item == '.' || $item == '..') continue;
			if (!delete_dir($dir.DIRECTORY_SEPARATOR.$item)) return false;
		}
		return rmdir($dir);
	}
	
	/**
	 * 
	 * @param unknown_type $array
	 * @param unknown_type $index
	 */
	public function array_remove_by_index($array, $index) {
		if (is_array($index)) {
			for($i=0; $i<count($array); $i++) {
				foreach ($index as $id) {
					unset($array[$id]);
				}
			}
		} else {
			for($i=0; $i<count($array); $i++) {
				unset($array[$index]);
			}
		}
		return array_values($array);
	}
	
	/**
	 * 
	 * @param unknown_type $system_dir
	 * @return string
	 */
	public function read_dir($system_dir) {
		$file_type = 'file';
		if (is_dir($system_dir)) {
			if ($dir = opendir($system_dir)) {
				while (($file = readdir($dir)) !== false) {
					if ($file != "." && $file != "..") {
						$dir_name[]['file'] = $file;
					}
				}
				$data['data'] = @$dir_name;
				return json_encode($data);
				closedir($dir);
			}
		}
	}
	
}