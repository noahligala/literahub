import * as pdfjsLib from 'pdfjs-dist';
import pdfWorkerUrl from 'pdfjs-dist/build/pdf.worker.mjs?url';

pdfjsLib.GlobalWorkerOptions.workerSrc =
    pdfWorkerUrl;


document.addEventListener(
    'DOMContentLoaded',
    () => {

        const reader =
            document.querySelector(
                '[data-pdf-reader]'
            );


        if (!reader) {
            return;
        }


        const url =
            reader.dataset.pdfUrl;


        if (!url) {
            return;
        }


        const canvas =
            reader.querySelector(
                '[data-pdf-canvas]'
            );


        const context =
            canvas.getContext(
                '2d'
            );


        const previousButton =
            reader.querySelector(
                '[data-page-previous]'
            );


        const nextButton =
            reader.querySelector(
                '[data-page-next]'
            );


        const pageInput =
            reader.querySelector(
                '[data-page-input]'
            );


        const pageCount =
            reader.querySelector(
                '[data-page-count]'
            );


        const zoomIn =
            reader.querySelector(
                '[data-zoom-in]'
            );


        const zoomOut =
            reader.querySelector(
                '[data-zoom-out]'
            );


        const zoomLabel =
            reader.querySelector(
                '[data-zoom-label]'
            );


        const loadingElement =
            reader.querySelector(
                '[data-reader-loading]'
            );


        const errorElement =
            reader.querySelector(
                '[data-reader-error]'
            );


        const stage =
            reader.querySelector(
                '[data-reader-stage]'
            );


        const initialPage =
            Number(
                reader.dataset.initialPage
                || 1
            );


        let pdfDocument =
            null;


        let pageNumber =
            Math.max(
                1,
                initialPage
            );


        let scale =
            1.25;


        let rendering =
            false;


        let pendingPage =
            null;


        /*
        |--------------------------------------------------------------------------
        | Render Page
        |--------------------------------------------------------------------------
        */

        const renderPage =
            async (
                number
            ) => {

                if (
                    !pdfDocument
                    ||
                    rendering
                ) {
                    pendingPage =
                        number;

                    return;
                }


                rendering =
                    true;


                try {

                    const page =
                        await pdfDocument.getPage(
                            number
                        );


                    const viewport =
                        page.getViewport({
                            scale
                        });


                    const outputScale =
                        window.devicePixelRatio
                        || 1;


                    canvas.width =
                        Math.floor(
                            viewport.width
                            * outputScale
                        );


                    canvas.height =
                        Math.floor(
                            viewport.height
                            * outputScale
                        );


                    canvas.style.width =
                        `${Math.floor(
                            viewport.width
                        )}px`;


                    canvas.style.height =
                        `${Math.floor(
                            viewport.height
                        )}px`;


                    const transform =
                        outputScale !== 1
                            ? [
                                outputScale,
                                0,
                                0,
                                outputScale,
                                0,
                                0,
                            ]
                            : null;


                    await page.render({
                        canvasContext:
                            context,

                        viewport,

                        transform,
                    }).promise;


                    pageNumber =
                        number;


                    pageInput.value =
                        pageNumber;


                    previousButton.disabled =
                        pageNumber <= 1;


                    nextButton.disabled =
                        pageNumber
                        >=
                        pdfDocument.numPages;


                    reader.dispatchEvent(
                        new CustomEvent(
                            'literahub:page-changed',
                            {
                                detail: {
                                    page:
                                        pageNumber,
                                },
                            }
                        )
                    );

                }
                catch (error) {

                    console.error(
                        'PDF render error:',
                        error
                    );


                    errorElement.hidden =
                        false;

                }
                finally {

                    rendering =
                        false;


                    if (
                        pendingPage !== null
                    ) {
                        const next =
                            pendingPage;

                        pendingPage =
                            null;

                        renderPage(
                            next
                        );
                    }

                }

            };


        /*
        |--------------------------------------------------------------------------
        | Load PDF
        |--------------------------------------------------------------------------
        */

        const load =
            async () => {

                try {

                    const loadingTask =
                        pdfjsLib.getDocument({
                            url,

                            withCredentials:
                                true,

                            rangeChunkSize:
                                65536,
                        });


                    pdfDocument =
                        await loadingTask.promise;


                    pageCount.textContent =
                        pdfDocument.numPages;


                    pageInput.max =
                        pdfDocument.numPages;


                    pageNumber =
                        Math.min(
                            Math.max(
                                pageNumber,
                                1
                            ),
                            pdfDocument.numPages
                        );


                    loadingElement.hidden =
                        true;


                    stage.hidden =
                        false;


                    await renderPage(
                        pageNumber
                    );

                }
                catch (error) {

                    console.error(
                        'Unable to load PDF:',
                        error
                    );


                    loadingElement.hidden =
                        true;


                    errorElement.hidden =
                        false;

                }

            };


        /*
        |--------------------------------------------------------------------------
        | Page Navigation
        |--------------------------------------------------------------------------
        */

        previousButton
            ?.addEventListener(
                'click',
                () => {

                    if (
                        pageNumber <= 1
                    ) {
                        return;
                    }


                    renderPage(
                        pageNumber - 1
                    );

                }
            );


        nextButton
            ?.addEventListener(
                'click',
                () => {

                    if (
                        !pdfDocument
                        ||
                        pageNumber
                        >=
                        pdfDocument.numPages
                    ) {
                        return;
                    }


                    renderPage(
                        pageNumber + 1
                    );

                }
            );


        pageInput
            ?.addEventListener(
                'change',
                () => {

                    if (!pdfDocument) {
                        return;
                    }


                    const requested =
                        Number(
                            pageInput.value
                        );


                    if (
                        Number.isNaN(
                            requested
                        )
                    ) {
                        pageInput.value =
                            pageNumber;

                        return;
                    }


                    renderPage(
                        Math.min(
                            Math.max(
                                requested,
                                1
                            ),
                            pdfDocument.numPages
                        )
                    );

                }
            );


        /*
        |--------------------------------------------------------------------------
        | Zoom
        |--------------------------------------------------------------------------
        */

        zoomIn
            ?.addEventListener(
                'click',
                () => {

                    scale =
                        Math.min(
                            scale + .15,
                            3
                        );


                    zoomLabel.textContent =
                        `${Math.round(
                            scale * 100
                        )}%`;


                    renderPage(
                        pageNumber
                    );

                }
            );


        zoomOut
            ?.addEventListener(
                'click',
                () => {

                    scale =
                        Math.max(
                            scale - .15,
                            .5
                        );


                    zoomLabel.textContent =
                        `${Math.round(
                            scale * 100
                        )}%`;


                    renderPage(
                        pageNumber
                    );

                }
            );


        /*
        |--------------------------------------------------------------------------
        | Keyboard Navigation
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'keydown',
            event => {

                if (
                    event.target
                        instanceof
                        HTMLInputElement
                    ||
                    event.target
                        instanceof
                        HTMLTextAreaElement
                ) {
                    return;
                }


                if (
                    event.key ===
                    'ArrowLeft'
                ) {
                    previousButton
                        ?.click();
                }


                if (
                    event.key ===
                    'ArrowRight'
                ) {
                    nextButton
                        ?.click();
                }

            }
        );


        load();

    }
);