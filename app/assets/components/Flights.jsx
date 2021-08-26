import React, { Component } from "react";
import SearchForm from "./SearchForm";
import { Container } from "semantic-ui-react";


class Flights extends Component {
  render() {
    return (
      <Container>
        <SearchForm />
      </Container>
    );
  }
}
export default Flights;
