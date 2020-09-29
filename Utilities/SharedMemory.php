<?php

namespace Extrablind\MonitHomeBundle\Utilities;

class SharedMemory
{
    public $shm;            //holds shared memory resource

    public function __construct()
    {
        if (false === \function_exists('shm_attach')) {
            die("\nYour PHP configuration needs adjustment. See: http://us2.php.net/manual/en/shmop.setup.php. To enable the System V shared memory support compile PHP with the option --enable-sysvshm.");
        }
        $this->attach();    //create resources (shared memory)
    }

    public function attach()
    {
        $this->shm = shm_attach(0x701da13b, 33554432);    //allocate shared memory
    }

    public function dettach()
    {
        return shm_detach($this->shm);    //allocate shared memory
    }

    public function remove()
    {
        return shm_remove($this->shm);    //dallocate shared memory
    }

    public function put($key, $var)
    {
        return shm_put_var($this->shm, $this->shm_key($key), $var);    //store var
    }

    public function get($key)
    {
        if ($this->has($key)) {
            return shm_get_var($this->shm, $this->shm_key($key));  //get var
        }

        return false;
    }

    public function del($key)
    {
        if ($this->has($key)) {
            return shm_remove_var($this->shm, $this->shm_key($key)); // delete var
        }

        return false;
    }

    public function has($key)
    {
        if (shm_has_var($this->shm, $this->shm_key($key))) { // check is isset
            return true;
        }

        return false;
    }

    public function shm_key($val)
    { // enable all world langs and chars !
    return preg_replace('/[^0-9]/', '', (preg_replace('/[^0-9]/', '', md5($val)) / 35676248) / 619876); // text to number system.
    }

    public function __wakeup()
    {
        $this->attach();
    }

    public function __destruct()
    {
        $this->dettach();
    }
}
