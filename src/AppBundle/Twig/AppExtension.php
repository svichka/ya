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
      switch ($prize)
      {
        case "dream":
          return "И участие в программе<br>«Успеть за 24 часа»!";
        case "certificate_lenina":
          return "Сертификат<br>3000";
        case "certificate_yves_rocher":
          return "Сертификат<br>4000";
        case "certificate_lamoda":
          return "Сертификат<br>3000";
        case "code_lenina":
          return "Скидка<br>30%";
        case "code_yves_rocher":
          return "Код<br>500р";
        case "code_lamoda":
          return "Код<br>600р";
      }
  
      return $prize;
    }
    
    public function prizeImageFilter($prize)
    {
      switch ($prize)
      {
        case "dream":
          return "dream";
        case "certificate_lenina":
          return "ll.png";
        case "certificate_yves_rocher":
          return "yr.png";
        case "certificate_lamoda":
          return "lamoda.png";
        case "code_lenina":
          return "ll.png";
        case "code_yves_rocher":
          return "yr.png";
        case "code_lamoda":
          return "lamoda.png";
      }
      
      return $prize;
    }
  }