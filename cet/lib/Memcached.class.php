<?php


class Memcached{

	/**
     *  Memcache资源句柄
     * @var resource
     */
    private $memcache;

    public function __construct($host='localhost',$port='11211'){
    	$this->memcache=new Memcache();
    	$this->memcache->connect($host,$port);
    	return $this->memcache;
    }

   /**  
     * 取出对应的key的value  
     *  
     * @param mixed $key  
     * @param mixed $flags  如果此值为1表示经过序列化,但未经过压缩，2表明压缩而未序列化，
     *     3表明压缩并且序列化，0表明未经过压缩和序列化  
     */ 
  	public function get($key,$flags=0){  
   		$value=$this->memcache->get($key,$flags);  
   		return $value;  
  	}  

   /**  
     * 存放值  
     *  
     * @param mixed $key  
     * @param mixed $var  
     * @param mixed $flag   默认为0不压缩  压缩状态填写：MEMCACHE_COMPRESSED  
     * @param mixed $expire  默认缓存时间(单位秒)  
     */ 
	public function set($key,$var,$flag=0,$expire=3600){      
	   $f=$this->memcache->set($key,$var,$flag,$expire);  
	   return empty($f)? 0:1;
	 }  

   /**  
     * 删除缓存的key  
     *  
     * @param mixed $key  
     * @param mixed $timeout  
     */ 
	public function delete($key,$timeout=1){  
	   $flag=$this->memcache->delete($key,$timeout);  
	   return $flag;  
	}  

   /**  
     * 刷新缓存但不释放内存空间  
     *  
     */ 
	public function flush(){  
	   $this->memcache->flush();  
	}

    /**  
     * 替换对应key的value  
     *  
     * @param mixed $key  
     * @param mixed $var  
     * @param mixed $flag  
     * @param mixed $expire  
     */ 
    public function replace($key,$var,$flag=0,$expire=3600){  
      $f=$this->memcache->replace($key,$var,$flag,$expire);  
      return $f;  
    }  

 	/**  
     * 关闭内存连接  
     *  
     */ 
    public function close(){  
     $this->memcache->close();  
    }  
}


