/**
 * @copyright Copyright (c) 2020 John Molakvoæ <skjnldsv@protonmail.com>
 *
 * @author John Molakvoæ <skjnldsv@protonmail.com>
 *
 * @license AGPL-3.0-or-later
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

 module.exports = {
	RULE_CSS: {
		test: /\.css$/,
		use: ['style-loader', 'css-loader'],
	},
	RULE_SCSS: {
		test: /\.scss$/,
		use: ['style-loader', 'css-loader', 'sass-loader'],
	},
	RULE_VUE: {
		test: /\.vue$/,
		loader: 'vue-loader',
	},
	RULE_JS: {
		test: /\.js$/,
		loader: 'babel-loader',
		exclude: /node_modules/,
	},
	RULE_TS: {
		test: /\.tsx?$/,
		loader: 'babel-loader',
		exclude: /node_modules/,
	},
	RULE_ASSETS: {
		test: /\.(png|jpe?g|gif|svg|woff2?|eot|ttf)$/,
		type: 'asset/inline',
	},
 }
