import React, {Component} from 'react';
import {Route, Switch,Redirect, Link} from 'react-router-dom';
import { Menu, Image } from 'semantic-ui-react'
import Flights from './Flights';
    
class Home extends Component {
  state = {};

  handleItemClick = (e, { name }) => this.setState({ activeItem: name });

  render() {
    const { activeItem } = this.state
    return (
      <div>
        <Menu>
        <Menu.Item header>
        <Image src='https://www.shippypro.com/sites/all/themes/shippypro_theme/new-assets/img/logos/shippypro.svg' size='small' />
        </Menu.Item>
          <Menu.Item
            as={Link}
            to={"/flights"}
            name="flights"
            active={activeItem === "flights"}
            onClick={this.handleItemClick}
          >
            Flights
          </Menu.Item>
        </Menu>
        <Switch>
          <Redirect exact from="/" to="/flights" />
          <Route path="/flights" component={Flights} />
        </Switch>
      </div>
    );
  }
}
    
export default Home;