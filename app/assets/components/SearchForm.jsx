import React, { Component } from "react";
import { Button, Divider, Form } from "semantic-ui-react";
import axios from "axios";
import ResultTable from "./ResultTable";

const stopoverOptions = [
  { text: "0", value: 0 },
  { text: "1", value: 1 },
  { text: "2", value: 2 },
];

class SearchForm extends Component {
  constructor() {
    super();
    this.state = { airports: [], loading: true, from: "", to: "", stopover: "", bestFlights: [] };
  }

  componentDidMount() {
    this.getAirports();
  }

  getAirports() {
    axios.get(`http://localhost/api/airports`).then((airports) => {
      this.setState({ airports: airports.data, loading: false });
    });
  }

  getBestPrice() {
    const { from, to, stopover } = this.state
    axios.get(`http://localhost/api/bestflights`, null, { params: {
      from,
      to,
      stopover
    }}).then((bestFlights) => {
      this.setState({ bestFlights: bestFlights.data, loading: false });
    });
    console.log('best', this.state.bestFlights)
  }

  handleChange = (name, value) => {
    this.setState({ [name]: value })
    console.log(name)
  }

  handleSubmit = () => {
    this.getBestPrice()
  }

  render() {
    const loading = this.state.loading;
    return (
      <>
        <Form onSubmit={this.handleSubmit}>
            <Form.Select
              name="from"
              fluid
              label="Departure Airport"
              options={this.state.airports}
              placeholder="Select an airport"
              onChange={(e, { value, name }) => this.handleChange(name, value)}
            />
            <Form.Select
              name="to"
              fluid
              label="Arrival Airport"
              options={this.state.airports}
              placeholder="Select an airport"
              onChange={(e, { value, name }) => this.handleChange(name, value)}
            />
            <Form.Select
            name="stopovers"
            name="stopover"
              fluid
              label="Stopovers"
              options={stopoverOptions}
              placeholder="Select a value"
              onChange={(e, { value, name }) => this.handleChange(name, value)}
            />
          <Button type="submit">Search</Button>
        </Form>
        <Divider />
        <ResultTable bestFlights={this.state.bestFlights} />
      </>
    );
  }
}
export default SearchForm;
