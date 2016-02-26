/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2016  (original work) Open Assessment Technologies SA;
 * 
 * @author Alexander Zagovorichev <zagovorichev@1pt.com>
 */

define([
    'jquery',
    'core/eventifier',
    'tpl!taoRestAPI/components/swagger/tpl/docs'
], function($, eventifier, DocsTpl) {
    'use strict';

    /**
     * @typedef {Object} swaggerDocs
     */
    
    /**
     * Creates an swaggerDoc component
     *
     * @param {jQueryElement} $container - where the list will be appended
     *
     * @returns {swaggerDocs} the component instance
     */
    return function swaggerDocs($container) {

        /**
         * Render the component
         * 
         * @returns {swaggerDocs} for chaining
         * @fires swaggerDocs#render - onLoad
         * @fires swaggerDocs#destroy - onDestroy
         */
        return eventifier({
            render: function render() {

                var $swaggerDocs = $(DocsTpl({
                    data: 'Documentation in here'
                }));
                
                $container.append($swaggerDocs);
                
                /**
                 * The swaggerDocs is rendered
                 * @event swaggerDocs#render
                 */
                this.trigger('render');

                return this;
            },

            /**
             * Leave the place as clean as before
             * @returns {swaggerDocs} for chaining
             * @fires swaggerDocs#destroy
             */
            destroy: function destroy() {

                $container
                    .find('.swagger-docs')
                    .remove();

                /**
                 * The swaggerDocs is destroyed
                 * @event swaggerDocs#destroy
                 */
                this.trigger('destroy');

                return this;
            }
        });
    }
});
