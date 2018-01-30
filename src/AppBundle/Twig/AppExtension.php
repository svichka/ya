<?php
  /**
   * Created by PhpStorm.
   * User: wrewolf
   * Date: 26.10.17
   * Time: 23:39
   */
  
  namespace AppBundle\Twig;
  
  class AppExtension extends \Twig_Extension
  {
    public function getFilters()
    {
      return [
        new \Twig_SimpleFilter('phone', [$this, 'phoneFilter']),
        new \Twig_SimpleFilter('prize', [$this, 'prizeFilter']),
        new \Twig_SimpleFilter('prize_image', [$this, 'prizeImageFilter']),
      ];
    }
    
    public function phoneFilter($num)
    {
      return ($num) ? '+7 (' . substr($num, 1, 3) . ') ' . substr($num, 4, 3) . '-' . substr($num, 7, 2) . '-' . substr($num, 9, 2) : '-';
    }
  
    public function prizeFilter($prize)
    {
      return $prize;
    }
  
    public function prizeImageFilter($prize)
    {
      return $prize;
    }
  }