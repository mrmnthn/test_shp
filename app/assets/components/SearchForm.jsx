import React, { Component } from "react";
import { Button, Divider, Form } from "semantic-ui-react";
import axios from "axios";
import ResultTable from "./ResultTable";

class SearchForm extends Component {
  constructor() {
    super();
    this.state = {
      airports: [],
      loading: true,
      from: "",
      to: "",
      stopover: "",
      bestFlights: [],
      stopoverOptions: [
        { text: "0", value: 0 },
        { text: "1", value: 1 },
        { text: "2", value: 2 },
      ]
    };
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
    const { from, to, stopover } = this.state;
    axios
      .get(`http://localhost/api/bestflights`, null, {
        params: {
          from,
          to,
          stopover,
        },
      })
      .then((bestFlights) => {
        this.setState({ bestFlights: bestFlights.data });
      });
  }

  handleDropdownChange = (name, value, e) => {
    this.setState({ [name]: value });

    console.log(e.target.innerText);
  };

  handleSubmit = (e) => {
    e.preventDefault();
    this.getBestPrice();
  };

  resetSearch = () =>
    this.setState({ from: "", to: "", stopover: "", bestFlights: [] });

  render() {
    const { airports, from, to, stopover, bestFlights, loading, stopoverOptions } = this.state
    return (
      <>
        <Form onSubmit={this.handleSubmit}>
          <Form.Select
            loading={loading}
            value={from}
            name="from"
            fluid
            label="Departure Airport"
            options={airports}
            placeholder="Select an airport"
            onChange={(e, { value, name }) => this.handleDropdownChange(name, value, e)}
          />
          <Form.Select
            loading={loading}
            value={to}
            name="to"
            fluid
            label="Arrival Airport"
            options={airports}
            placeholder="Select an airport"
            onChange={(e, { value, name }) => this.handleDropdownChange(name, value)}
          />
          <Form.Select
            value={stopover}
            name="stopover"
            fluid
            label="Stopovers"
            options={stopoverOptions}
            placeholder="Select a value"
            onChange={(e, { value, name }) => this.handleDropdownChange(name, value)}
          />
          <Button 
            color="green" 
            type="submit" 
            disabled={!from
            || !to
            }
            >Search</Button>
          <Button  color="pink"  onClick={this.resetSearch}>Reset</Button>
        </Form>
        <Divider />
        <ResultTable 
          bestFlights={bestFlights} 
          from={from} 
          to={to} 
          />
      </>
    );
  }
}
export default SearchForm;
