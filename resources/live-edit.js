function initLiveEdit() {
    function getArticle() {
        let article = document.querySelector('#content > article');

        if (article === null) {
            // If no article element is found the user may have a custom template, so we cannot know which element to edit.
            throw new Error('No article element found, cannot live edit. If you are using a custom template, please make sure to include an article element in the #content container.');
        }

        return article;
    }

    function getLiveEditor() {
        return document.querySelector('#live-edit-container');
    }

    function showEditor() {
        article.style.display = 'none';
        getLiveEditor().style.display = '';
        focusOnTextarea();
    }

    function hideEditor() {
        article.style.display = '';
        getLiveEditor().style.display = 'none';
    }

    function focusOnTextarea() {
        const textarea = getLiveEditor().querySelector('textarea');

        textarea.selectionStart = textarea.value.length;
        textarea.focus();
    }

    function switchToEditor() {

        function hasEditorBeenSetUp() {
            return getLiveEditor() !== null;
        }

        function setupEditor() {
            const template = document.getElementById('live-edit-template');
            const article = getArticle();
            let editor = document.importNode(template.content, true);
            article.parentNode.insertBefore(editor, article.nextSibling);
            editor = getLiveEditor();

            // Apply CSS classes from article to editor to match layout
            editor.classList.add(...article.classList);

            showEditor();

            document.getElementById('liveEditCancel').addEventListener('click', hideEditor);
        }

        if (hasEditorBeenSetUp()) {
            showEditor();
        } else {
            setupEditor();
        }
    }

    function handleShortcut(event) {
        let isEditorHidden = getLiveEditor() === null || getLiveEditor().style.display === 'none';
        let isEditorVisible = getLiveEditor() !== null && getLiveEditor().style.display !== 'none';

        if (event.ctrlKey && event.key === 'e') {
            event.preventDefault();

            if (isEditorHidden) {
                switchToEditor();
            } else {
                hideEditor();
            }
        }

        if (event.ctrlKey && event.key === 's') {
            if (isEditorVisible) {
                event.preventDefault();

                document.getElementById('liveEditSubmit').click();
            }
        }

        if (event.key === 'Escape') {
            if (isEditorVisible) {
                event.preventDefault();

                hideEditor();
            }
        }
    }

    function shortcutsEnabled() {
        return localStorage.getItem('hydephp.live-edit.shortcuts') !== 'false';
    }

    const article = getArticle();

    article.addEventListener('dblclick', switchToEditor);

    if (shortcutsEnabled()) {
        document.addEventListener('keydown', handleShortcut);
    }
}
