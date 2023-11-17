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

            document.getElementById('liveEditForm').addEventListener('submit', handleFormSubmit);
        }

        if (hasEditorBeenSetUp()) {
            showEditor();
        } else {
            setupEditor();
        }
    }

    function handleFormSubmit(event) {
        event.preventDefault();

        fetch('/_hyde/live-edit', {
            method: "POST",
            body: new FormData(event.target),
            headers: new Headers({
                "Accept": "application/json",
            }),
        }).then(async response => {
            if (response.ok) {
                window.location.reload();
            } else {
                if (response.status === 409) {
                    if (confirm('This page has been modified in another window. Do you want to overwrite the changes?')) {
                        document.getElementById('liveEditForm').insertAdjacentHTML('beforeend', '<input type="hidden" name="force" value="true">');
                        document.getElementById('liveEditForm').submit();
                    }
                    return;
                }

                alert(`Error saving content: ${response.status} ${response.statusText}\n${JSON.parse(await response.text()).error ?? 'Unknown error'}`);
            }
        });
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
