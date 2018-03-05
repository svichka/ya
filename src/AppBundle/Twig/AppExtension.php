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
        new \Twig_SimpleFilter('agpf', [$this, 'prizeAGImageFilter']),
        new \Twig_SimpleFilter('awpf', [$this, 'prizeAWImageFilter']),
        new \Twig_SimpleFilter('public_email', [$this, 'emailFilter']),
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
          return "И участие в программе «Успеть за 24 часа»!";
        
        case "certificate_lenina":
        case "moda_lenina_weekly":
          return "Сертификат 3000 ₽";
        
        case "certificate_yves_rocher":
        case "moda_yves_rocher_weekly":
          return "Сертификат 3000 ₽";
        
        case "certificate_lamoda":
        case "moda_lamoda_weekly":
          return "Сертификат 4000 ₽";
        
        case "code_lenina":
        case "moda_lenina_guaranteed":
          return "Скидка 30%";
        
        case "code_yves_rocher":
        case "moda_yves_rocher_guaranteed":
          return "Скидка 500 ₽";
        
        case "code_lamoda":
        case "moda_lamoda_guaranteed":
          return "Скидка 600 ₽";
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
    
    public function prizeAGImageFilter($prize)
    {
      switch ($prize)
      {
        case "ll":
          return "Лена Ленина: Скидка 30%";
        case "yr":
          return "Ив Роше:  Скидка 500р";
        case "lamoda":
          return "Ламода: Скидка 600р";
      }
      
      return $prize;
    }
    
    public function prizeAWImageFilter($prize)
    {
      switch ($prize)
      {
        case "ll":
          return "Лена Ленина: Сертификат 3000Р";
        case "yr":
          return "Ив Роше: Сертификат 4000Р";
        case "lamoda":
          return "Ламода: Сертификат 3000Р";
      }
      
      return $prize;
    }
    
    public function emailFilter($email)
    {
      $pos = strpos($email, '@');
      if ($pos === false)
      {
        return $email;
      }
      if ($pos > 3)
      {
        $t = "";
        for ($i = 3; $i <= $pos; $i++)
        {
          $t .= "*";
        }
        
        return substr($email, 0, 2) . $t . substr($email, $pos);
      }
      else
      {
        return substr($email, 0, 1) . "*" . substr($email, $pos);
      }
    }
  }