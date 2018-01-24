<?php
  
  namespace AppBundle\Controller;
  
  use Dalee\PEPUWSClientBundle\Exception\ApiFailedException;
  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\File\UploadedFile;
  use Symfony\Component\HttpFoundation\Request;
  
  use AppBundle\Form\Type\Feedback\NotAuthorizedUserFormType;
  use AppBundle\Form\Type\Feedback\AuthorizedUserFormType;
  use Symfony\Component\Security\Acl\Exception\Exception;
  use Symfony\Component\Validator\Constraints as Assert;
  use Symfony\Component\Form\Extension\Core\Type\TextType;
  use Symfony\Component\Form\Extension\Core\Type\EmailType;
  use Symfony\Component\Form\Extension\Core\Type\PasswordType;
  use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
  use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
  use Symfony\Component\Form\Extension\Core\Type\ButtonType;
  use Symfony\Component\Form\Extension\Core\Type\SubmitType;
  
  use Dalee\PEPUWSClientBundle\Controller\FeedbackApiController;
  use Dalee\PEPUWSClientBundle\Exception\NotCorrectDataException;
  
  class FeedbackController extends Base
  {
    private $messages = [];
    private $errors = [];
    
    /**
     * @Route("/feedback", name="feedback_page")
     */
    public function feedbackAction(Request $request)
    {
      
      NotAuthorizedUserFormType::$em = $this->getDoctrine()->getRepository('AppBundle:Theme')->findAll();
      $this->get('logger')->error("init!");
      $formInputData = [''];
      $user = $this->getUser();
      if ($user)
      {
        $formInputData['user_id'] = $user->getParticipant()->id;
        $formInputData['email'] = $user->getParticipant()->email;
        $formInputData['agree'] = true;
        $form = $this->createForm(AuthorizedUserFormType::class, $formInputData);
      }
      else
      {
        $form = $this->createForm(NotAuthorizedUserFormType::class, $formInputData);
      }
      
      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid())
      {
        $formData = $form->getData();
        try
        {
          if ($formData['agree'] == 'N')
          {
            throw new NotCorrectDataException('Укажите согласие на обработку данных');
          }
          
          $recaptcha = $this->container->get('app.recaptcha');
          if (!$user && !$recaptcha->isSuccess($request))
          {
            $this->get('logger')->error('recaptcha error');
            throw new NotCorrectDataException('Каптча не заполнена');
          }
          
          
          if ($formData['file'] instanceof UploadedFile)
          {
            $this->get('logger')->info('add file');
            $formData['file_name'] = $formData['file']->getClientOriginalName();
            $formData['file_type'] = $formData['file']->getClientOriginalExtension();
            try
            {
              $file = $formData['file']->openFile();
              $contents = $file->fread($file->getSize());
              $file_data = base64_encode($contents);
              $formData['file'] = $file_data;
            }
            catch (Exception $e)
            {
              throw new NotCorrectDataException('Ошибка загрузки файла');
            }
          }
          else
          {
            unset($formData['file']);
          }
          
          $this->get('logger')->info('formData' . print_r($formData, true));
          $feedbackApi = new FeedbackApiController();
          unset($formData[0]);
          unset($formData['agree']);
          unset($formData['recaptcha']);
          $ticketNumber = $feedbackApi->add($formData);
          
          $this->addFlash(
            'feedback',
            'ok'
          );
          
          return $this->redirectToRoute('feedback_page');
        }
        catch (NotCorrectDataException $e)
        {
          $this->get('logger')->error($e->getMessage());
          if ($e->getMessage() == 'Incorrect request data' || $e->getMessage() == null || $e->getMessage() == '')
          {
            $this->errors[] = "Ошибка отправки данных.";
          }
          else
          {
            $this->errors[] = $e->getMessage();
          }
          $this->addFlash(
            'feedback_error',
            'ok'
          );
        }
        catch (ApiFailedException $e2)
        {
          $this->get('logger')->error('feedback error ' . print_r($formData, true));
          $this->errors[] = "Внутренняя ошибка сервера";
          $this->addFlash(
            'feedback_error',
            'ok'
          );
        }
      }
      
      return $this->render('AppBundle:Default:feedback.html.twig', ['errors' => $this->errors,
                                                                    'form'   => $form->createView(),]);
    }
  }
