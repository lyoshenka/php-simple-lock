<?php

namespace SimpleLock;

class Lock
{
  protected static $lockDir = null;

  /**
   * Acquire an advisory lock.
   *
   * @param string $name A string identifying the lock.
   * @param boolean $blocking Whether or not to block until lock is acquired
   *
   * @return resource|false Return name of lockfile if lock was acquired, false otherwise.
   */
  public static function acquire($name, $blocking = false)
  {
    if (!preg_match('/^[A-Za-z0-9\.\-_]+$/', $name))
    {
      throw new InvalidArgumentException('Invalid lock name: "' . $name . '"');
    }

    $filename = static::getLockDir() . '/' . $name;
    if (!preg_match('/\.lo?ck$/', $filename))
    {
      $filename .= '.lock';
    }

    if (!file_exists($filename))
    {
      file_put_contents($filename, '');
      chmod($filename, 0666); // if file is not readable later, locking will fail
    }

    $lockFile = fopen($filename, 'c');

    if (!flock($lockFile, $blocking ? LOCK_EX : LOCK_EX|LOCK_NB))
    {
      fclose($lockFile);
      return false;
    }

    return $lockFile;
  }

  /**
   * Free a lock acquired with acquire().
   *
   * @param resource $lockFile The lockfile that was returned by acquire()
   **/
  public static function release($lockFile)
  {
    if ($lockFile)
    {
      flock($lockFile, LOCK_UN);
      fclose($lockFile);
    }
  }

  /**
   * Get the directory where lockfiles will be stored
   *
   * @return string The lockfile directory
   */
  public static function getLockDir()
  {
    $dir = static::$lockDir ?: sys_get_temp_dir();
    if (!is_dir($dir))
    {
      mkdir($dir);
      chmod($dir, 0777);
    }
    return $dir;
  }

  /**
   * Set the directory where lockfiles will be stored
   *
   * @param string $lockDir The lockfile directory
   */
  public static function setLockDir($lockDir)
  {
    static::$lockDir = rtrim($lockDir,'/');
  }
}
