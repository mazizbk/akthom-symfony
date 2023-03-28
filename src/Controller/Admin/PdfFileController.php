<?php

namespace App\Controller\Admin;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\ElasticService;
use App\Repository\PdfFileRepository;
use App\Form\PdfFileType;
use App\Form\MetaType;
use App\Event\PdfFileProcessedEvent;
use App\Entity\PdfFile;

#[Route('/admin/pdf-file')]
class PdfFileController extends AbstractController
{
    #[Route('/', name: 'app_pdf_file_index', methods: ['GET'])]
    public function index(PdfFileRepository $pageRepository): Response
    {
        return $this->render('admin/pdffile/index.html.twig', [
            'pdfFile' => $pageRepository->findAll(),
        ]);
    }

    #[Route('/upload', name: 'app_pdf_file_upload', methods: ['GET', 'POST'])]
    public function upload(Request $request, PdfFileRepository $pdfFileRepository, SluggerInterface $slugger, MessageBusInterface $bus, ElasticService $elasticService): Response
    {
        $pdfFile = new PdfFile();

        $form = $this->createForm(PdfFileType::class, $pdfFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('filename')->getData();
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            $safeFilename = $slugger->slug($originalFilename);
            $extension = $file->guessExtension();
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $extension;
            $file->move(
                $this->getParameter('pdf_aktehom_directory'),
                $newFilename
            );

            $validFile = $this->isPdfValid($newFilename, $extension, $validFileName, $password);
            if ($validFile === 0) {


                $pdfFile->setStatus(true);
                $pdfFile->setProtected($password ? \true : \false);
                $pdfFile->setFilename($validFileName);
                $pdfFile->setPassword($password);
                $pdfFile->setPath($this->getParameter('pdf_aktehom_directory') . '/' . $validFileName);
                $pdfFileRepository->save($pdfFile, true);

                // Déclencher l'événement PdfFileProcessedEvent
                $event = new PdfFileProcessedEvent($pdfFile->getFilename());
                $bus->dispatch($event);
            } else {
                $this->addFlash('error', 'Le fichier PDF ne peut pas être déverouillé aucun mot de passe ne correspond.');
                return $this->redirectToRoute('app_pdf_file_upload');
            }


            return $this->redirectToRoute('app_pdf_file_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/pdffile/upload.html.twig', [
            'form' => $form,
        ]);
    }

    private function isPdfValid($newFilename, $extension, &$validFileName, &$password)
    {
        $newFilenameLockPath = $this->getParameter('pdf_aktehom_directory') . '/' . $newFilename;
        $command = "qpdf --show-encryption $newFilenameLockPath";
        $output = array();
        \exec($command, $output);
        $returnValue = 0;
        $validFileName = $newFilename;
        if (implode("\n", $output) === "") {
            $passwords = ['aziz1', 'aziz2', 'aziz3', 'aziz', 'aziz4', 'davidson'];

            $validFileName = str_replace('.' . $extension, "_unlock." . $extension, $newFilename);
            $newFilenamePath =  $this->getParameter('pdf_aktehom_directory') . "/" . $validFileName;
            foreach ($passwords as $password) {

                $command = "qpdf --decrypt --password=$password $newFilenameLockPath " . $newFilenamePath;

                exec($command, $output, $returnValue);
                if ($returnValue === 0) {
                    break;
                }
            }
        }
        return $returnValue;
    }

    #[Route('/{id}/meta', name: 'app_pdf_file_meta', methods: ['GET', 'POST'])]
    public function edit(Request $request, PdfFile $pdfFile, PdfFileRepository $pdfFileRepository, ElasticService $elasticService): Response
    {
        $fileName = $pdfFile->getFilename();

        $meta  = ($pdfFile->getMetadata()) ? json_decode($pdfFile->getMetadata()) : $elasticService->getMetaByFileName('pdf_aktehom', $fileName);

        $form = $this->createForm(MetaType::class, ['metadata' => $meta]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData('meta');
            unset($data['metadata']);
            $pdfFile->setMetadata(\json_encode($data));
            $pdfFileRepository->save($pdfFile, true);

            return $this->redirectToRoute('app_pdf_file_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/pdffile/meta.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_pdf_file_delete', methods: ['POST'])]
    public function delete(Request $request, PdfFile $pdfFile, PdfFileRepository $pdfFileRepository, ElasticService $elasticService): Response
    {

        if ($this->isCsrfTokenValid('delete' . $pdfFile->getId(), $request->request->get('_token'))) {
            $elasticService->deleteByFileName($pdfFile->getFilename());
            $pdfFileRepository->remove($pdfFile, true);

            return $this->redirectToRoute('app_pdf_file_index', [], Response::HTTP_SEE_OTHER);
        }
    }
}
