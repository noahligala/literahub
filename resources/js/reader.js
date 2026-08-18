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



        /*
        |--------------------------------------------------------------------------
        | Reader Elements
        |--------------------------------------------------------------------------
        */

        const canvas =
            reader.querySelector(
                '[data-pdf-canvas]'
            );


        if (!canvas) {
            return;
        }


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



        /*
        |--------------------------------------------------------------------------
        | Table of Contents Elements
        |--------------------------------------------------------------------------
        */

        const tocToggle =
            reader.querySelector(
                '[data-toc-toggle]'
            );


        const tocPanel =
            reader.querySelector(
                '[data-reader-toc]'
            );


        const tocClose =
            reader.querySelector(
                '[data-toc-close]'
            );


        const tocBody =
            reader.querySelector(
                '[data-toc-body]'
            );


        const tocLoading =
            reader.querySelector(
                '[data-toc-loading]'
            );



        /*
        |--------------------------------------------------------------------------
        | Initial State
        |--------------------------------------------------------------------------
        */

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


        let outlineLoaded =
            false;


        let outlineLinks =
            [];



        /*
        |--------------------------------------------------------------------------
        | Update Active Table of Contents Entry
        |--------------------------------------------------------------------------
        */

        const updateActiveOutline =
            (
                currentPage
            ) => {

                if (
                    !outlineLinks.length
                ) {
                    return;
                }


                outlineLinks.forEach(
                    link => {

                        link.classList.remove(
                            'is-active'
                        );

                    }
                );


                /*
                 * Select the final TOC destination at or
                 * before the current page.
                 */

                let activeLink =
                    null;


                const sortedLinks =
                    [...outlineLinks]
                        .sort(
                            (
                                first,
                                second
                            ) => {

                                return (
                                    Number(
                                        first.dataset.page
                                    )
                                    -
                                    Number(
                                        second.dataset.page
                                    )
                                );

                            }
                        );


                for (
                    const link
                    of sortedLinks
                ) {

                    const target =
                        Number(
                            link.dataset.page
                        );


                    if (
                        target <=
                        currentPage
                    ) {
                        activeLink =
                            link;
                    }
                    else {
                        break;
                    }

                }


                if (!activeLink) {
                    return;
                }


                activeLink.classList.add(
                    'is-active'
                );


                /*
                 * Only scroll the TOC itself when it is visible.
                 */

                if (
                    tocPanel
                    &&
                    !tocPanel.hidden
                ) {
                    activeLink.scrollIntoView({
                        block:
                            'nearest',

                        behavior:
                            'smooth',
                    });
                }

            };



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


                /*
                 * Prevent invalid destinations.
                 */

                number =
                    Math.min(
                        Math.max(
                            Number(number),
                            1
                        ),
                        pdfDocument.numPages
                    );


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


                    if (pageInput) {
                        pageInput.value =
                            pageNumber;
                    }


                    if (previousButton) {
                        previousButton.disabled =
                            pageNumber <= 1;
                    }


                    if (nextButton) {
                        nextButton.disabled =
                            pageNumber
                            >=
                            pdfDocument.numPages;
                    }


                    /*
                     * Highlight the current chapter.
                     */

                    updateActiveOutline(
                        pageNumber
                    );


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


                    if (errorElement) {
                        errorElement.hidden =
                            false;
                    }

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
        | Resolve PDF Outline Destination
        |--------------------------------------------------------------------------
        */

        const resolveDestinationPage =
            async (
                destination
            ) => {

                if (
                    !pdfDocument
                    ||
                    !destination
                ) {
                    return null;
                }


                let explicitDestination =
                    destination;


                /*
                 * Some outlines use a named destination.
                 */

                if (
                    typeof destination
                    === 'string'
                ) {

                    try {

                        explicitDestination =
                            await pdfDocument
                                .getDestination(
                                    destination
                                );

                    }
                    catch (error) {

                        console.warn(
                            'Unable to resolve named PDF destination:',
                            destination,
                            error
                        );


                        return null;

                    }

                }


                if (
                    !Array.isArray(
                        explicitDestination
                    )
                ) {
                    return null;
                }


                const destinationReference =
                    explicitDestination[0];


                /*
                 * Direct numeric page destination.
                 */

                if (
                    typeof destinationReference
                    === 'number'
                ) {
                    return (
                        destinationReference
                        + 1
                    );
                }


                /*
                 * Standard PDF page reference.
                 */

                if (
                    destinationReference
                    &&
                    typeof destinationReference
                    === 'object'
                ) {

                    try {

                        const pageIndex =
                            await pdfDocument
                                .getPageIndex(
                                    destinationReference
                                );


                        return (
                            pageIndex
                            + 1
                        );

                    }
                    catch (error) {

                        console.warn(
                            'Unable to resolve PDF outline page:',
                            error
                        );

                    }

                }


                return null;

            };



        /*
        |--------------------------------------------------------------------------
        | Create One Outline Tree
        |--------------------------------------------------------------------------
        */

        const createOutlineList =
            async (
                items
            ) => {

                const list =
                    document.createElement(
                        'ul'
                    );


                list.className =
                    'reader-toc-list';


                for (
                    const item
                    of items
                ) {

                    const listItem =
                        document.createElement(
                            'li'
                        );


                    listItem.className =
                        'reader-toc-item';


                    const row =
                        document.createElement(
                            'div'
                        );


                    row.className =
                        'reader-toc-row';



                    /*
                    |--------------------------------------------------------------------------
                    | Expand / Collapse Button
                    |--------------------------------------------------------------------------
                    */

                    let childrenContainer =
                        null;


                    if (
                        Array.isArray(
                            item.items
                        )
                        &&
                        item.items.length
                    ) {

                        const expander =
                            document.createElement(
                                'button'
                            );


                        expander.type =
                            'button';


                        expander.className =
                            'reader-toc-expander is-open';


                        expander.textContent =
                            '›';


                        expander.setAttribute(
                            'aria-label',
                            `Toggle ${
                                item.title
                                || 'section'
                            }`
                        );


                        expander.setAttribute(
                            'aria-expanded',
                            'true'
                        );


                        childrenContainer =
                            document.createElement(
                                'div'
                            );


                        childrenContainer.className =
                            'reader-toc-children';


                        expander.addEventListener(
                            'click',
                            event => {

                                event.preventDefault();
                                event.stopPropagation();


                                const willOpen =
                                    childrenContainer.hidden;


                                childrenContainer.hidden =
                                    !childrenContainer.hidden;


                                expander.classList.toggle(
                                    'is-open',
                                    willOpen
                                );


                                expander.setAttribute(
                                    'aria-expanded',
                                    willOpen
                                        ? 'true'
                                        : 'false'
                                );

                            }
                        );


                        row.appendChild(
                            expander
                        );

                    }
                    else {

                        const spacer =
                            document.createElement(
                                'span'
                            );


                        spacer.style.width =
                            '22px';


                        spacer.style.flex =
                            '0 0 22px';


                        spacer.setAttribute(
                            'aria-hidden',
                            'true'
                        );


                        row.appendChild(
                            spacer
                        );

                    }



                    /*
                    |--------------------------------------------------------------------------
                    | Chapter Link
                    |--------------------------------------------------------------------------
                    */

                    const link =
                        document.createElement(
                            'button'
                        );


                    link.type =
                        'button';


                    link.className =
                        'reader-toc-link';


                    const title =
                        document.createElement(
                            'span'
                        );


                    title.textContent =
                        item.title
                        || 'Untitled section';


                    link.appendChild(
                        title
                    );


                    const targetPage =
                        await resolveDestinationPage(
                            item.dest
                        );


                    if (
                        targetPage !== null
                    ) {

                        link.dataset.page =
                            String(
                                targetPage
                            );


                        const pageLabel =
                            document.createElement(
                                'span'
                            );


                        pageLabel.className =
                            'reader-toc-page';


                        pageLabel.textContent =
                            targetPage;


                        link.appendChild(
                            pageLabel
                        );


                        outlineLinks.push(
                            link
                        );


                        link.addEventListener(
                            'click',
                            async () => {

                                await renderPage(
                                    targetPage
                                );


                                /*
                                 * On mobile the contents sidebar
                                 * should close after navigation.
                                 */

                                if (
                                    window.innerWidth
                                    <= 800
                                    &&
                                    tocPanel
                                ) {

                                    tocPanel.hidden =
                                        true;


                                    tocToggle
                                        ?.setAttribute(
                                            'aria-expanded',
                                            'false'
                                        );

                                }

                            }
                        );

                    }
                    else {

                        /*
                         * Some outline headings do not have a
                         * destination and only group children.
                         */

                        if (
                            !item.items
                            ||
                            !item.items.length
                        ) {

                            link.disabled =
                                true;

                        }

                    }


                    row.appendChild(
                        link
                    );


                    listItem.appendChild(
                        row
                    );



                    /*
                    |--------------------------------------------------------------------------
                    | Nested Sections
                    |--------------------------------------------------------------------------
                    */

                    if (
                        childrenContainer
                        &&
                        item.items.length
                    ) {

                        const nested =
                            await createOutlineList(
                                item.items
                            );


                        childrenContainer.appendChild(
                            nested
                        );


                        listItem.appendChild(
                            childrenContainer
                        );

                    }


                    list.appendChild(
                        listItem
                    );

                }


                return list;

            };



        /*
        |--------------------------------------------------------------------------
        | Load PDF Table of Contents
        |--------------------------------------------------------------------------
        */

        const loadOutline =
            async () => {

                if (
                    !pdfDocument
                    ||
                    outlineLoaded
                    ||
                    !tocBody
                ) {
                    return;
                }


                outlineLoaded =
                    true;


                try {

                    const outline =
                        await pdfDocument
                            .getOutline();


                    if (tocLoading) {
                        tocLoading.remove();
                    }


                    /*
                     * The PDF has no embedded outline.
                     */

                    if (
                        !Array.isArray(
                            outline
                        )
                        ||
                        !outline.length
                    ) {

                        const empty =
                            document.createElement(
                                'div'
                            );


                        empty.className =
                            'reader-toc__empty';


                        empty.innerHTML =
                            `
                                <strong>
                                    No contents available
                                </strong>

                                <p>
                                    This book does not contain an
                                    embedded table of contents.
                                </p>
                            `;


                        tocBody.appendChild(
                            empty
                        );


                        return;

                    }


                    /*
                     * Build tree.
                     */

                    const list =
                        await createOutlineList(
                            outline
                        );


                    tocBody.appendChild(
                        list
                    );


                    /*
                     * Highlight initial/current page.
                     */

                    updateActiveOutline(
                        pageNumber
                    );

                }
                catch (error) {

                    console.error(
                        'Unable to load PDF contents:',
                        error
                    );


                    if (tocLoading) {
                        tocLoading.remove();
                    }


                    tocBody.innerHTML =
                        `
                            <div class="reader-toc__empty">

                                <strong>
                                    Contents unavailable
                                </strong>

                                <p>
                                    The document contents could not
                                    be loaded.
                                </p>

                            </div>
                        `;

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

                            /*
                             * Required for PDFs containing
                             * JBIG2/OpenJPEG encoded content.
                             */
                            wasmUrl:
                                '/pdfjs/wasm/',
                        });


                    pdfDocument =
                        await loadingTask.promise;


                    if (pageCount) {
                        pageCount.textContent =
                            pdfDocument.numPages;
                    }


                    if (pageInput) {
                        pageInput.max =
                            pdfDocument.numPages;
                    }


                    pageNumber =
                        Math.min(
                            Math.max(
                                pageNumber,
                                1
                            ),
                            pdfDocument.numPages
                        );


                    if (loadingElement) {
                        loadingElement.hidden =
                            true;
                    }


                    if (stage) {
                        stage.hidden =
                            false;
                    }


                    /*
                     * Load TOC after PDF metadata is available.
                     */

                    await loadOutline();


                    await renderPage(
                        pageNumber
                    );

                }
                catch (error) {

                    console.error(
                        'Unable to load PDF:',
                        error
                    );


                    if (loadingElement) {
                        loadingElement.hidden =
                            true;
                    }


                    if (errorElement) {
                        errorElement.hidden =
                            false;
                    }

                }

            };



        /*
        |--------------------------------------------------------------------------
        | Table of Contents Open / Close
        |--------------------------------------------------------------------------
        */

        tocToggle
            ?.addEventListener(
                'click',
                async () => {

                    if (!tocPanel) {
                        return;
                    }


                    if (!outlineLoaded) {
                        await loadOutline();
                    }


                    const willOpen =
                        tocPanel.hidden;


                    tocPanel.hidden =
                        !willOpen;


                    tocToggle.setAttribute(
                        'aria-expanded',
                        willOpen
                            ? 'true'
                            : 'false'
                    );


                    if (willOpen) {

                        updateActiveOutline(
                            pageNumber
                        );

                    }

                }
            );


        tocClose
            ?.addEventListener(
                'click',
                () => {

                    if (!tocPanel) {
                        return;
                    }


                    tocPanel.hidden =
                        true;


                    tocToggle
                        ?.setAttribute(
                            'aria-expanded',
                            'false'
                        );

                }
            );



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


                    if (zoomLabel) {
                        zoomLabel.textContent =
                            `${Math.round(
                                scale * 100
                            )}%`;
                    }


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


                    if (zoomLabel) {
                        zoomLabel.textContent =
                            `${Math.round(
                                scale * 100
                            )}%`;
                    }


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


                /*
                 * Previous Page
                 */

                if (
                    event.key ===
                    'ArrowLeft'
                ) {

                    event.preventDefault();


                    previousButton
                        ?.click();


                    return;

                }


                /*
                 * Next Page
                 */

                if (
                    event.key ===
                    'ArrowRight'
                ) {

                    event.preventDefault();


                    nextButton
                        ?.click();


                    return;

                }


                /*
                 * Toggle Contents
                 */

                if (
                    event.key ===
                    'F4'
                ) {

                    event.preventDefault();


                    tocToggle
                        ?.click();


                    return;

                }


                /*
                 * Escape closes the contents sidebar.
                 */

                if (
                    event.key ===
                    'Escape'
                    &&
                    tocPanel
                    &&
                    !tocPanel.hidden
                ) {

                    tocPanel.hidden =
                        true;


                    tocToggle
                        ?.setAttribute(
                            'aria-expanded',
                            'false'
                        );

                }

            }
        );



        /*
        |--------------------------------------------------------------------------
        | Start Reader
        |--------------------------------------------------------------------------
        */

        load();

    }
);