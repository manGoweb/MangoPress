import componentHandler from './componentHandler'
import InView from './components/InView'
import Shapes from './components/Shapes'
import Example from './components/Example'
import './plugins'

componentHandler([Example, InView, Shapes], 'initComponents')
