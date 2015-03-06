<?php

namespace system;

class sessionEx {

    private $config = array();

    public $userdata = array();

    public function __construct() {
        $this->config['cookie_name']     = 'zuoye';
        $this->config['expiration']      = 7200;
        $this->config['expire_on_close'] = FALSE;
        $this->config['encrypt_cookie']  = FALSE;
        $this->config['use_database']    = FALSE;
        $this->config['match_ip']        = FALSE;
        $this->config['match_useragent'] = TRUE;
        $this->config['time_to_update']  = 3000;
        $this->config['cookie_prefix']    = ""; 
        $this->config['cookie_domain']    = "zhineng.yewenlong.com"; 
        $this->config['cookie_path']      = "/";
        $this->config['cookie_secure']    = FALSE;



		if ( ! $this->sess_read())
		{
			$this->sess_create();
		}
		else
		{
			$this->sess_update();
		}
    }

	public function sess_write()
	{
		$this->_set_cookie($this->userdata);
		//$custom_userdata = $this->userdata;
		//$cookie_userdata = array();
		//foreach (array('session_id','ip_address','user_agent','last_activity') as $val)
		//{
		//	unset($custom_userdata[$val]);
		//	$cookie_userdata[$val] = $this->userdata[$val];
		//}

		//if (count($custom_userdata) === 0)
		//{
		//	$custom_userdata = '';
		//}
		//else
		//{
		//	$custom_userdata = $this->_serialize($custom_userdata);
		//}
        //var_dump($custom_userdata);
        //var_dump($cookie_userdata);

		//$this->_set_cookie($cookie_userdata);
	}

	function sess_update()
	{
		$old_sessid = $this->userdata['session_id'];
		$new_sessid = '';
		while (strlen($new_sessid) < 32)
		{
			$new_sessid .= mt_rand(0, mt_getrandmax());
		}

        $new_sessid .=($_SERVER["HTTP_VIA"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];

		$new_sessid = md5(uniqid($new_sessid, TRUE));

		$this->userdata['session_id'] = $new_sessid;
		$this->userdata['last_activity'] = time();

		$cookie_data = NULL;

		$this->_set_cookie($cookie_data);
	}

	function set_userdata($newdata = array(), $newval = '')
	{
		if (is_string($newdata))
		{
			$newdata = array($newdata => $newval);
		}

		if (count($newdata) > 0)
		{
			foreach ($newdata as $key => $val)
			{
				$this->userdata[$key] = $val;
			}
		}

		$this->sess_write();
	}

	function userdata($item)
	{
		return ( ! isset($this->userdata[$item])) ? FALSE : $this->userdata[$item];
	}

    public function sess_read() {

        $session = $_COOKIE[$this->config['cookie_name']] ? $_COOKIE[$this->config['cookie_name']] : "";
        if ( $session != "" ) {
            return TRUE;
        }
        return FALSE; 
    }

	public function sess_create()
	{
		$sessid = '';
		while (strlen($sessid) < 32)
		{
			$sessid .= mt_rand(0, mt_getrandmax());
		}
        $sessid .=($_SERVER["HTTP_VIA"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];

		$this->userdata = array(
							'session_id'	=> md5(uniqid($sessid, TRUE)),
							'ip_address'	=> ($_SERVER["HTTP_VIA"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"],
							'user_agent'	=> array(),
							'last_activity'	=> "",
							'user_data'		=> ''
							);
		$this->_set_cookie($this->userdata);

	}

	function _set_cookie($cookie_data = NULL)
	{
		if (is_null($cookie_data))
		{
			$cookie_data = $this->userdata;
		}
		$cookie_data = $this->_serialize($cookie_data);
		$cookie_data .= hash_hmac('sha1', $cookie_data, "code001");
		$expire = 86400 + time();
		setcookie(
			$this->config['cookie_name'],
			$cookie_data,
			$expire,
			$this->config['cookie_path'],
			$this->config['cookie_domain'],
			$this->config['cookie_secure']
		);
	}

	public function set_cookie($name = '', $value = '', $expire = '', $domain = '', $path = '/', $prefix = '', $secure = FALSE)
	{
		if (is_array($name))
		{
			// always leave 'name' in last place, as the loop will break otherwise, due to $$item
			foreach (array('value', 'expire', 'domain', 'path', 'prefix', 'secure', 'name') as $item)
			{
				if (isset($name[$item]))
				{
					$$item = $name[$item];
				}
			}
		}

		if ( ! is_numeric($expire))
		{
			$expire = time() - 86500;
		}
		else
		{
			$expire = ($expire > 0) ? time() + $expire : 0;
		}

		setcookie($prefix.$name, $value, $expire, $path, $domain, $secure);
	}

	public function _serialize($data)
	{
		if (is_array($data))
		{
			foreach ($data as $key => $val)
			{
				if (is_string($val))
				{
					$data[$key] = str_replace('\\', '{{slash}}', $val);
				}
			}
		}
		else
		{
			if (is_string($data))
			{
				$data = str_replace('\\', '{{slash}}', $data);
			}
		}

		return serialize($data);
	}

	public function _unserialize($data)
	{
		$data = @unserialize(strip_slashes($data));

		if (is_array($data))
		{
			foreach ($data as $key => $val)
			{
				if (is_string($val))
				{
					$data[$key] = str_replace('{{slash}}', '\\', $val);
				}
			}

			return $data;
		}

		return (is_string($data)) ? str_replace('{{slash}}', '\\', $data) : $data;
	}

	function sess_destroy()
	{
		setcookie(
					$this->config['cookie_name'],
					addslashes(serialize(array())),
					(time() - 31500000),
					$this->config['cookie_path'],
					$this->config['cookie_domain'],
					0
				);
		$this->userdata = array();
	}
}
