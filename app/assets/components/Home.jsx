import React, {Component} from 'react';
import {Route, Switch,Redirect, Link} from 'react-router-dom';
import { Menu } from 'semantic-ui-react'
import Users from './Users';
    
class Home extends Component {
  state = {};

  handleItemClick = (e, { name }) => this.setState({ activeItem: name });

  render() {
    const { activeItem } = this.state
    return (
      <div>
        <Menu>
          <Menu.Item
            as={Link}
            to={"/users"}
            name="users"
            active={activeItem === "users"}
            onClick={this.handleItemClick}
          >
            Users
          </Menu.Item>
        </Menu>
        <Switch>
          <Redirect exact from="/" to="/users" />
          <Route path="/users" component={Users} />
        </Switch>
      </div>
    );
  }
}
    
export default Home;