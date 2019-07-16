import { initializeComponents } from '@mangoweb/scripts-base'

import './plugins'

import { Example } from './components/Example'
import { Shapes } from '@mangoweb/scripts-base'

// Sort the components alphabetically…
initializeComponents([Example, Shapes], 'initComponents')
