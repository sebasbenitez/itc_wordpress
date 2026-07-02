/**
 * WoodMart Images Optimizer
 */
(function($) {
    'use strict';

    const CONFIG = {
        SELECTORS: {
            optimizeButton: '.xts-imgopt-optimize-btn',
            restoreButton: '.xts-imgopt-restore-btn',
            bulkProgress: '#bulk-progress',
            bulkProgressBar: '#bulk-progress-bar',
            optimizerContainer: '.xts-imgopt-container'
        },
        CLASSES: {
            processing: 'processing'
        },
        TIMEOUTS: {
            bulkDelay: 1000,
            pageReload: 2000
        },
        MESSAGES: {
            optimizing: 'Optimizing...',
            restoring: 'Restoring...'
        }
    };

    /**
     * Base AJAX handler class
     */
    class AjaxHandler {
        async request(options) {
            const defaultOptions = {
                url: xts_optimizer.ajax_url,
                type: 'POST',
                data: { nonce: xts_optimizer.nonce }
            };

            return new Promise((resolve, reject) => {
                $.ajax({
                    ...defaultOptions,
                    ...options,
                    success: resolve,
                    error: (xhr, status, error) => reject({ xhr, status, error })
                });
            });
        }
    }

    /**
     * Individual image optimizer
     */
    class ImageOptimizer extends AjaxHandler {
        constructor() {
            super();
            $(document).on('click', CONFIG.SELECTORS.optimizeButton, this.handleOptimize.bind(this));
        }

        async handleOptimize(event) {
            event.preventDefault();
            
            const button = $(event.currentTarget);
            const container = button.closest(CONFIG.SELECTORS.optimizerContainer);
            const imageId = button.data('id');

            if (button.hasClass(CONFIG.CLASSES.processing)) {
                return;
            }

            button.addClass(CONFIG.CLASSES.processing).text(CONFIG.MESSAGES.optimizing);

            try {
                const response = await this.request({
                    data: {
                        action: 'xts_optimizer_run',
                        image_id: imageId,
                        nonce: xts_optimizer.nonce
                    }
                });

                if (!response.success) {
                    const errorMessage = response.data?.message || response.data?.result?.message || 'Optimization failed';
                    alert('Error: ' + errorMessage);
                    button.removeClass(CONFIG.CLASSES.processing).text('Optimize');
                    return;
                }

                // Replace container with HTML from server (works for both success and error)
                if (response.data?.html) {
                    container.replaceWith(response.data.html);
                }
            } catch (error) {
                // Network error - re-enable button
                button.removeClass(CONFIG.CLASSES.processing).text('Optimize');
            }
        }
    }

    /**
     * Image restore handler
     */
    class ImageRestorer extends AjaxHandler {
        constructor() {
            super();
            $(document).on('click', CONFIG.SELECTORS.restoreButton, this.handleRestore.bind(this));
        }

        async handleRestore(event) {
            event.preventDefault();
            
            const button = $(event.currentTarget);
            const container = button.closest(CONFIG.SELECTORS.optimizerContainer);
            const imageId = button.data('id');

            if (button.hasClass(CONFIG.CLASSES.processing)) {
                return;
            }

            if (!confirm('Are you sure you want to restore the original image? This will replace the optimized version.')) {
                return;
            }

            button.addClass(CONFIG.CLASSES.processing).text(CONFIG.MESSAGES.restoring);

            try {
                const response = await this.request({
                    data: {
                        action: 'xts_optimizer_restore',
                        image_id: imageId,
                        nonce: xts_optimizer.nonce
                    }
                });

                // Replace container with HTML from server (works for both success and error)
                if (response.data?.html) {
                    container.replaceWith(response.data.html);
                }
            } catch (error) {
                // Network error - re-enable button
                button.removeClass(CONFIG.CLASSES.processing).text('Restore backup');
            }
        }
    }

    /**
     * Bulk optimization handler
     */
    class BulkOptimizer extends AjaxHandler {
        constructor() {
            super();
            this.isProcessing = false;
            this.processed = 0;
            this.errors = 0;
            this.successes = 0;
        }

        initialize() {
            if (typeof window.woodmartBulkOptimize === 'undefined') {
                return;
            }

            this.batchId = window.woodmartBulkOptimize.batch_id;
            this.total = window.woodmartBulkOptimize.total;
            this.resetCounters();
            this.startProcessing();
        }

        resetCounters() {
            this.processed = 0;
            this.errors = 0;
            this.successes = 0;
        }

        async startProcessing() {
            if (this.isProcessing) {
                return;
            }

            this.isProcessing = true;
            await this.processBatch(0);
        }

        async processBatch(offset) {
            try {
                const response = await this.request({
                    data: {
                        action: 'xts_optimizer_bulk',
                        batch_id: this.batchId,
                        offset: offset,
                        nonce: xts_optimizer.nonce
                    }
                });

                this.handleBatchResponse(response);
            } catch (error) {
                this.handleBatchError();
            }
        }

        handleBatchResponse(response) {
            if (!response.success) {
                this.showBatchError(response.data || 'Unknown error');
                return;
            }

            const { data } = response;
            this.processed = data.processed;

            // Count results
            data.results.forEach(result => {
                if (result.success) {
                    this.successes++;
                } else {
                    this.errors++;
                }
            });

            this.updateProgress(data.progress_percentage);

            if (data.complete) {
                this.handleBatchCompletion();
            } else {
                this.scheduleContinuation();
            }
        }

        updateProgress(progressPercentage) {
            $(CONFIG.SELECTORS.bulkProgress).text(this.processed);
            $(`${CONFIG.SELECTORS.bulkProgressBar} div`).css('width', `${progressPercentage}%`);
        }

        handleBatchCompletion() {
            const message = this.buildCompletionMessage();
            const progressContainer = $(CONFIG.SELECTORS.bulkProgressBar).parent();
            progressContainer.html(`<p style="color: green;"><strong>${message}</strong></p>`);
            this.schedulePageRedirect();
        }

        buildCompletionMessage() {
            let message = `Bulk optimization complete! ${this.successes} images optimized`;
            if (this.errors > 0) {
                message += `, ${this.errors} errors occurred`;
            }
            return message;
        }

        scheduleContinuation() {
            setTimeout(() => {
                this.processBatch(this.processed);
            }, CONFIG.TIMEOUTS.bulkDelay);
        }

        schedulePageRedirect() {
            setTimeout(() => {
                const url = window.location.href.replace(/[?&](bulk_optimize|image_count)=[^&]*/g, '');
                window.location.href = url;
            }, CONFIG.TIMEOUTS.pageReload);
        }

        showBatchError(errorMessage) {
            const progressContainer = $(CONFIG.SELECTORS.bulkProgressBar).parent();
            progressContainer.html(`<p style="color: red;"><strong>Error: ${errorMessage}</strong></p>`);
        }

        handleBatchError() {
            const progressContainer = $(CONFIG.SELECTORS.bulkProgressBar).parent();
            progressContainer.html('<p style="color: red;"><strong>Network error occurred during bulk optimization.</strong></p>');
        }
    }

    // Initialize when DOM is ready
    $(document).ready(() => {
        new ImageOptimizer();
        new ImageRestorer();
        new BulkOptimizer().initialize();
    });

})(jQuery);