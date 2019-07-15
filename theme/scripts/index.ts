import { initializeComponents } from '@mangoweb/scripts-base'

import './plugins'

import { Example } from './components/Example'
import { Shapes } from '@mangoweb/scripts-base/lib/components/Shapes'

// Sort the components alphabetically…
initializeComponents([Example, Shapes] as any, 'initComponents')
