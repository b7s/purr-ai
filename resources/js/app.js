import "./bootstrap";
import "./toast";
import "./audio-devices";
import "./speech-recognition";
import "./chat-stream";
import "./code-copy";
import "./code-highlight";
import "./timezone-selector";

document.addEventListener("alpine:init", () => {
    Alpine.data(
        "historyDropdown",
        (
            initialConversations,
            initialHasMorePages,
            initialSearchQuery = ""
        ) => ({
            open: false,
            editModalOpen: false,
            editingId: null,
            editingTitle: "",
            loadingMore: false,
            conversations: initialConversations,
            hasMorePages: initialHasMorePages,
            searchOpen: Boolean(initialSearchQuery),
            searchTerm: initialSearchQuery,
            searchDebounceTimer: null,

            init() {
                this.$watch("searchTerm", (value) => {
                    this.handleSearchTermChange(value);
                });

                if (this.searchOpen) {
                    this.$nextTick(() => this.focusSearchInput());
                }

                window.addEventListener("conversations-updated", (event) => {
                    if (event.detail && event.detail.conversations) {
                        this.conversations = event.detail.conversations;
                    }
                });
            },

            getLivewireComponent() {
                // Find the Chat component specifically (it has the messages-container)
                const chatContainer =
                    document.getElementById("messages-container");
                if (chatContainer) {
                    const livewireEl = chatContainer.closest("[wire\\:id]");
                    if (livewireEl) {
                        const wireId = livewireEl.getAttribute("wire:id");
                        return Livewire.find(wireId);
                    }
                }
                return null;
            },

            startNewConversation() {
                const component = this.getLivewireComponent();
                if (component) {
                    component.call("newConversation");
                }
            },

            deleteConversation(id) {
                const component = this.getLivewireComponent();
                if (component) {
                    component.call("deleteConversation", id).then(() => {
                        this.conversations = this.conversations.filter(
                            (c) => c.id !== id
                        );
                    });
                }
            },

            deleteAllConversations() {
                const component = this.getLivewireComponent();
                if (component) {
                    component.call("deleteAllConversations").then(() => {
                        this.conversations = [];
                    });
                }
            },

            renameConversation(id) {
                const component = this.getLivewireComponent();
                if (component) {
                    component.call("renameConversation", id);
                }
            },

            loadConv(id) {
                const component = this.getLivewireComponent();
                if (component) {
                    component.call("loadConversation", id);
                }
            },

            focusSearchInput() {
                this.$refs.historySearchInput?.focus();
                this.$refs.historySearchInput?.select?.();
            },

            toggleSearch() {
                if (this.searchOpen) {
                    this.closeSearch();

                    return;
                }

                this.searchOpen = true;
                this.$nextTick(() => this.focusSearchInput());
            },

            closeSearch() {
                this.searchOpen = false;
                this.searchTerm = "";
                this.syncSearchQuery("");
            },

            handleSearchTermChange(value) {
                clearTimeout(this.searchDebounceTimer);
                this.searchDebounceTimer = setTimeout(() => {
                    this.syncSearchQuery(value);
                }, 300);
            },

            syncSearchQuery(value) {
                const component = this.getLivewireComponent();
                if (component) {
                    component.set("searchQuery", (value ?? "").trim());
                }
            },

            startEdit(id, title) {
                this.editingId = id;
                this.editingTitle = title;
                this.editModalOpen = true;
                this.$nextTick(() => {
                    this.$refs.editInput?.select();
                });
            },

            saveEdit() {
                if (!this.editingTitle.trim()) {
                    return;
                }

                const component = this.getLivewireComponent();
                if (component) {
                    component
                        .call(
                            "saveTitleDirect",
                            this.editingId,
                            this.editingTitle
                        )
                        .then(() => {
                            const conv = this.conversations.find(
                                (c) => c.id === this.editingId
                            );
                            if (conv) {
                                conv.title = this.editingTitle;
                            }
                            this.editModalOpen = false;
                            this.editingId = null;
                            this.editingTitle = "";
                        });
                }
            },

            cancelEdit() {
                this.editModalOpen = false;
                this.editingId = null;
                this.editingTitle = "";
            },

            loadMore() {
                this.loadingMore = true;
                const component = this.getLivewireComponent();
                if (component) {
                    component
                        .call("loadMoreHistoryConversations")
                        .then((newConversations) => {
                            if (
                                newConversations &&
                                newConversations.length > 0
                            ) {
                                this.conversations = [
                                    ...this.conversations,
                                    ...newConversations,
                                ];
                                this.hasMorePages =
                                    newConversations.length >= 10;
                            } else {
                                this.hasMorePages = false;
                            }
                            this.loadingMore = false;
                        })
                        .catch(() => {
                            this.loadingMore = false;
                        });
                } else {
                    this.loadingMore = false;
                }
            },
        })
    );
});
